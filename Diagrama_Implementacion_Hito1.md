# MIRA — Diagrama de implementación (despliegue)

**Proyecto:** MIRA – Mapa de Incidencias, Reportes y Atención
**Equipo:** Bit Oriental — Grupo TC4
**Hito:** 1 — 18/09/2026
**Referencia:** secciones 4.14.4 (Arquitectura), 4.17 (POSIX), 4.18 (Redes Microsoft), 4.19 (Conectividad) y 4.21 (Infraestructura y despliegue)

---

## 1. Diagrama de implementación general (nodos y zonas de red)

```mermaid
flowchart TB
    subgraph INET["INTERNET"]
        CIU["Ciudadano<br/>navegador movil / PC"]
        ADMREM["Administrador remoto<br/>VPN IPsec IKEv2"]
        OPENAI["API externa de IA<br/>api.openai.com : 443"]
    end

    subgraph PERIM["ZONA PERIMETRAL"]
        ASA["Firewall Cisco ASA 5505<br/>WAN 200.10.10.2<br/>INSIDE 10.10.x.1 - DMZ 10.10.40.1<br/>NAT/PAT - ACL deny by default - VPN IPsec"]
    end

    subgraph DMZ["DMZ — 10.10.40.0/24"]
        WEB["<b>SRV-WEB-MIRA</b> — 10.10.40.10<br/>device: Servidor web<br/>Nginx proxy reverso + TLS<br/>Puertos 80/443 publicados"]
        BAST["Bastion — 10.10.40.20"]
    end

    subgraph CORE["NUCLEO DE RED"]
        SW3560["Switch Core Cisco 3560<br/>Inter-VLAN SVI - Trunk 802.1Q - RSTP"]
        SW2960["Switches de acceso Cisco 2960<br/>VLAN 1 y VLAN 2"]
    end

    subgraph VLAN1["VLAN 1 — Administradores — 10.10.10.0/24"]
        PCADM["PC Administracion TI<br/>DHCP .10–.254<br/>SSH / RDP / SNMP"]
    end

    subgraph VLAN2["VLAN 2 — Usuarios — 10.10.20.0/24"]
        PCOP["PC Operador municipal<br/>DHCP .10–.254<br/>HTTPS al panel MIRA"]
    end

    subgraph VLAN3["VLAN 3 — Servidores — 10.10.30.0/24"]
        POSIX["<b>SRV-POSIX</b> — 10.10.30.14<br/>device: Ubuntu Server 24.04 LTS<br/>Docker + Docker Compose<br/>ufw - Fail2ban - cron + pg_dump"]
        AD["<b>SRV-AD-DNS</b> — 10.10.30.13<br/>device: Windows Server 2022<br/>AD DS mira.local - DNS - DHCP - GPO<br/>Puertos 53, 88, 389, 445"]
    end

    CIU -->|"HTTPS 443"| ASA
    ADMREM -->|"Tunel IPsec<br/>AES-256 / DH-14"| ASA
    ASA -->|"DNAT 80/443 Permitir"| WEB
    ASA -.->|"Internet hacia LAN: DENEGAR"| SW3560
    WEB -->|"HTTPS 443<br/>unica regla DMZ hacia INSIDE"| POSIX
    POSIX -->|"NAT/PAT 443"| OPENAI

    ASA --- SW3560
    SW3560 --- SW2960
    SW2960 --- PCADM
    SW2960 --- PCOP
    SW3560 --- POSIX
    SW3560 --- AD

    PCOP -->|"443 panel interno"| POSIX
    PCOP -->|"DNS 53 - LDAP 389 - DHCP 67"| AD
    PCADM -->|"22, 3389, 5432, 5985<br/>administracion"| POSIX
    PCADM -->|"53, 88, 389, 445, 3389"| AD
    POSIX <-->|"Resolucion DNS mira.local"| AD

    classDef dmz fill:#fff3e0,stroke:#e65100,stroke-width:2px,color:#000
    classDef srv fill:#ffebee,stroke:#b71c1c,stroke-width:2px,color:#000
    classDef adm fill:#e3f2fd,stroke:#0d47a1,stroke-width:2px,color:#000
    classDef usr fill:#e8f5e9,stroke:#1b5e20,stroke-width:2px,color:#000
    classDef fw  fill:#ede7f6,stroke:#4527a0,stroke-width:2px,color:#000
    class WEB,BAST dmz
    class POSIX,AD srv
    class PCADM adm
    class PCOP usr
    class ASA fw
```

---

## 2. Artefactos desplegados en SRV-POSIX (contenedores Docker)

```mermaid
flowchart TB
    EXT["SRV-WEB-MIRA (DMZ)<br/>10.10.40.10"]

    subgraph NODO["device: SRV-POSIX — 10.10.30.14 — Ubuntu Server 24.04 LTS"]
        UFW["ufw<br/>443 desde DMZ y VLAN 2<br/>22 solo VLAN 1 - 5432 solo local"]
        subgraph DOCKER["execution environment: Docker Engine + Docker Compose"]
            NGX["container: nginx<br/>Proxy reverso interno<br/>expone 443"]
            APP["container: app<br/>artifact: MIRA Laravel 13 / PHP 8.4<br/>Controllers - Services - Eloquent<br/>Blade + Leaflet"]
            DB[("container: db<br/>PostgreSQL 16 + PostGIS<br/>Indice espacial GIST<br/>5432 solo red interna")]
            QUEUE["container: queue (opcional)<br/>Colas y notificaciones"]
        end
        VOL[("volume: Almacenamiento<br/>Imagenes de incidencias<br/>/var/lib/mira/storage")]
        BKP["artifact: cron + pg_dump<br/>Backup diario - retencion 7 dias"]
    end

    EXT -->|"HTTPS 443"| UFW
    UFW --> NGX
    NGX -->|"FastCGI / HTTP interno"| APP
    APP -->|"SQL / PostGIS"| DB
    APP --> QUEUE
    APP -->|"read/write"| VOL
    DB -.->|"pg_dump nocturno"| BKP

    classDef cont fill:#e1f5fe,stroke:#01579b,stroke-width:1.5px,color:#000
    classDef data fill:#fff8e1,stroke:#f57f17,stroke-width:1.5px,color:#000
    class NGX,APP,QUEUE cont
    class DB,VOL,BKP data
```

---

## 3. Vista por capas de la aplicación

```mermaid
flowchart LR
    subgraph P["Capa de Presentacion"]
        V["Vistas Blade + JavaScript<br/>Leaflet sobre tiles OpenStreetMap"]
    end
    subgraph A["Capa de Aplicacion — Laravel 13"]
        API["API interna<br/>POST /api/incidencias<br/>GET /api/incidencias.geojson<br/>PATCH /api/incidencias/id/estado<br/>GET /api/estadisticas"]
        C["Controladores<br/>Auth - Incidencia - Panel - Admin"]
        S["Servicios<br/>IncidenciaService<br/>NotificacionService"]
    end
    subgraph D["Capa de Datos"]
        E["Eloquent ORM<br/>User - Incidencia - Categoria<br/>Area - HistorialIncidencia"]
        PG[("PostgreSQL 16 + PostGIS<br/>geometry - indice GIST")]
    end

    V --> API --> C --> S --> E --> PG
```

---

## 4. Flujo integral del sistema desplegado (escenario 4.22.2)

```mermaid
sequenceDiagram
    autonumber
    participant CIU as Ciudadano
    participant FW as ASA 5505
    participant WEB as SRV-WEB-MIRA (DMZ)
    participant APP as App Laravel (SRV-POSIX)
    participant IA as API de IA
    participant DB as PostgreSQL/PostGIS
    participant OP as Operador (VLAN 2)

    CIU->>FW: HTTPS 443 hacia srv-web-mira
    FW->>WEB: DNAT 443 (regla de publicacion)
    WEB->>APP: Proxy HTTPS 443 (DMZ hacia VLAN 3)
    CIU->>APP: Formulario + ubicacion en mapa + foto
    APP->>IA: Sugerencia de categoria y area (NAT/PAT 443)
    IA-->>APP: Categoria y area responsable
    APP->>DB: INSERT incidencia (geometry, indice GIST)
    DB-->>APP: Codigo unico de seguimiento
    APP-->>CIU: Incidencia registrada
    OP->>APP: HTTPS 443 al panel interno (VLAN 2 hacia VLAN 3)
    APP->>DB: Cambio de estado + historial
    Note over OP,DB: Pendiente - En proceso - Resuelto
    CIU->>APP: Consulta de seguimiento
```

---

## 5. Tabla de nodos del diagrama

| Nodo | Equipo / SO | Zona | IP | Software desplegado | Puertos |
|---|---|---|---|---|---|
| SRV-WEB-MIRA | Servidor web | DMZ | 10.10.40.10 | Nginx (proxy reverso + TLS) | 80/443 externa |
| SRV-POSIX | Ubuntu Server 24.04 LTS | VLAN 3 | 10.10.30.14 | Docker: app Laravel 13, Nginx, PostgreSQL 16 + PostGIS | 443 interno · 5432 local |
| SRV-AD-DNS | Windows Server 2022 | VLAN 3 | 10.10.30.13 | AD DS (mira.local), DNS, DHCP, GPO | 53, 88, 389, 445 |
| Bastión | Host de salto | DMZ | 10.10.40.20 | Acceso administrado | 22 |
| Firewall ASA 5505 | Cisco (licencia Security Plus) | Perimetral | 200.10.10.2 / 10.10.40.1 | ACL, NAT/PAT, VPN IPsec | — |
| Switch Core 3560 | Cisco | Núcleo | — | Inter-VLAN (SVI), trunk 802.1Q, RSTP | — |
| Switches 2960 | Cisco | Acceso | — | Acceso VLAN 1 y 2 | — |
| PC Operador | Cliente | VLAN 2 | DHCP 10.10.20.10–254 | Navegador | 443 |
| PC Administración | Cliente TI | VLAN 1 | DHCP 10.10.10.10–254 | SSH, RDP, monitoreo SNMP | 22, 3389 |

---

## 6. Procedimiento de despliegue (4.21.3)

```mermaid
flowchart LR
    S1["1. git clone en SRV-POSIX<br/>docker compose up -d --build"]
    S2["2. php artisan migrate --seed<br/>migraciones y seeders"]
    S3["3. Nginx en SRV-WEB-MIRA<br/>proxy hacia SRV-POSIX"]
    S4["4. Registros A en DNS<br/>mira.local"]
    S5["5. Verificacion de conectividad<br/>y politicas de firewall"]
    S6["6. Respaldo inicial<br/>de la base de datos"]
    S1 --> S2 --> S3 --> S4 --> S5 --> S6
```

---

### Cómo visualizar y exportar

- **GitHub / GitLab:** renderizan Mermaid automáticamente al abrir el `.md`.
- **VS Code:** extensión *Markdown Preview Mermaid Support* (Ctrl+Shift+V).
- **Obsidian / Typora:** soporte nativo.
- **Exportar a imagen** para pegar en el documento integrador (que se entrega en Word/PDF): [mermaid.live](https://mermaid.live) → pegar el bloque → *Actions → PNG/SVG*.
