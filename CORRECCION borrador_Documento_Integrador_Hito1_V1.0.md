REPÚBLICA ORIENTAL DEL URUGUAY 

**INSTITUTO TECNOLÓGICO DE INFORMÁTICA (ITI)** 

**CTT TECNICATURA EN REDES Y SOFTWARE — GRUPO TC4** 

**DOCUMENTO INTEGRADOR** 

**Versión de control documental del proyecto** 

**MIRA – MAPA DE INCIDENCIAS, REPORTES Y ATENCIÓN** Equipo: **Bit Oriental**   
**Hito 1** — Entrega 18/09/2026 

**Carrera** CTT – Tecnicatura en Redes y Software — TC4 **Grupo / Equipo** TC4 / Bit Oriental 

**Coordinador** Thomas Galo 

**Subcoordinador** Fabio Fernández 

**Docentes tutores** Martín Viar (Registro Documental · Gestión de Proyectos), Christian Menéndez (Programación), Julio González (Base 

de Datos), Gregor Rodríguez (POSIX · Redes MS), Mario   
Malagrino (Conectividad y Seguridad), Carina Sindoni   
(Testing) 

**Hito** Hito 1 — 18/09/2026 

| Fecha  | Versión  | Descripción del cambio  | Responsable |
| :---: | :---: | :---: | :---: |
| 01/09/2026  | V0.1  | Creación del documento integrador: carátula, resumen ejecutivo y estructura ITI. | Thomas Galo |
| 08/09/2026  | V0.5  | Incorporación de metodología Scrum, roles, herramientas y avance del relevamiento. | Fabio  Fernández |
| 16/09/2026  | V0.9  | Candidato a final: secciones de red, base de datos, POSIX, Redes MS y pruebas. Revisión interna del equipo. | Riccardo  Inojosa |
| 18/09/2026  | V1.0  | Versión final aprobada para entrega en Campus Virtual.  |  |

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_ 

Firma del coordinador 

1 / 33  
**ÍNDICE DE CONTENIDOS** 

4.1 Resumen ejecutivo 1 4.2 Introducción 1 4.3 Planteamiento del problema 2 4.4 Objetivos del proyecto 3 4.5 Alcance, exclusiones y limitaciones 4 4.6 Factibilidad y viabilidad 5 4.7 Análisis FODA y gestión de riesgos 6 4.8 Entregables e hitos 7 4.9 Planificación y seguimiento 8 4.10 Organización y recursos 9 4.11 Metodología de trabajo 10 4.12 Procedimientos, herramientas y tecnología 11 4.13 Relevamiento y requisitos 11 4.14 Análisis y diseño 13 4.15 Desarrollo e implementación de software 14 4.16 Base de datos 14 4.17 POSIX (servidor Linux) 17 4.18 Redes Microsoft 18 4.19 Conectividad y seguridad de redes 19 4.20 Pruebas y validación 21 4.21 Infraestructura, despliegue y soporte 22 4.22 Solución integrada 23 4.23 Conclusiones 23 4.24 Bibliografía (normas APA) 24 4.25 Anexos 25 (Anexo H — ESRE según IEEE 830) 27 4.26 Hoja testigo 26 Anexo documental — Control de versiones 26 4.27 Matriz de alineación con la rúbrica 2026 27 7\. Tablas mínimas de gestión 27 4.28 Evidencias técnicas mínimas 30 4.29 Checklist de entrega (Hito 1\) 30 

2 / 33  
**4.1 Resumen ejecutivo** 

El presente documento integra la documentación del proyecto académico **MIRA (Mapa de Incidencias, Reportes y Atención)**, desarrollado por el equipo **Bit Oriental** (grupo TC4 de la Tecnicatura en Redes y Software del Instituto Tecnológico de Informática) para la entrega del **Hito 1**. 

MIRA es un sistema web para el **registro y gestión de incidencias de limpieza urbana** de la ciudad de Montevideo. El sistema permite que los ciudadanos reporten incidencias (contenedores desbordados, acumulación de residuos, residuos fuera de los contenedores, entre otras) con **descripción, ubicación geográfica y evidencia fotográfica**; a partir de allí, el ciudadano selecciona la categoría de la incidencia, que tiene asociada un área responsable y que el administrador puede modificar, facilitando la gestión por parte de la Intendencia de Montevideo. 

El proyecto aborda un problema real: los canales de comunicación actuales no están especializados para este tipo de reportes, la información llega desorganizada o incompleta y no existe una fuente centralizada con datos geográficos que permita detectar zonas críticas y planificar la recolección. 

Para el Hito 1 se presenta la **planificación consolidada del proyecto**: la definición metodológica (Scrum), la organización del equipo y sus roles, el cronograma con hitos, y el **avance inicial del relevamiento de requerimientos**, que incluye el análisis de los canales actuales de la Intendencia y la definición preliminar de los requisitos funcionales y no funcionales. 

El equipo propone para la implementación un stack moderno y de bajo costo de adopción: **PHP 8.4 con Laravel 13**, servidor web **Nginx**, base de datos **PostgreSQL con la extensión geoespacial PostGIS**, servicios contenerizados con **Docker** y un mapa interactivo para la geolocalización. La infraestructura de red se diseña bajo un esquema de **segmentación por VLAN** (administración, usuarios y servidores) con una **DMZ** para el servidor web y control perimetral mediante firewall Cisco **ASA 5505** y VPN IPsec. 

Las siete dimensiones de la rúbrica 2026 cuentan con un plan de abordaje; la dimensión de **Conectividad y Seguridad de Redes** es la que presenta mayor madurez en esta etapa, con la topología, el plan de direccionamiento y las políticas de seguridad ya documentadas. 

**4.2 Introducción** 

La ciudad de Montevideo gestiona la limpieza urbana a través de la Intendencia de Montevideo (IM), entidad que atiende el reclamo ciudadano por múltiples canales: teléfono, formularios web de consultas, medios presenciales y redes sociales (Intendencia de Montevideo, s.f.). La gestión de incidencias de limpieza —contenedores desbordados, basura acumulada, contenedores fuera de lugar, camadas y puntos críticos— es una de las tareas con mayor demanda de atención y de la que dependen las políticas de planificación de la recolección. 

El proyecto **MIRA** surge de un relevamiento inicial sobre estos canales: los reportes llegan por vías no especializadas, con información incompleta (falta de ubicación precisa o de evidencia), no se clasifican de forma uniforme y no existe una base centralizada de tipo georreferenciado que permita visualizar la distribución de las incidencias sobre un mapa de la ciudad. Como consecuencia, los tiempos de respuesta y la posibilidad de detectar zonas críticas se ven limitados. 

En respuesta, el equipo Bit Oriental propone un **sistema web de registro, clasificación y gestión de incidencias de limpieza urbana**, accesible para los ciudadanos y de uso interno para los operadores de la IM. La categoría la selecciona el ciudadano al reportar y define el área responsable; el administrador puede modificar ambas. 

3 / 33  
El presente documento corresponde al **Hito 1** del proyecto académico, entregado según la plantilla ITI versión 2.0 y la rúbrica de evaluación 2026 de la Tecnicatura en Redes y Software. Su propósito es integrar el plan de trabajo, la organización del equipo, la metodología, el relevamiento inicial de requisitos y el avance de las siete dimensiones evaluadas (registro documental, gestión de proyecto, programación, base de datos, POSIX, Redes Microsoft y conectividad y seguridad de redes). 

El documento está organizado en veintiséis secciones obligatorias (4.1–4.26) que siguen la estructura de la plantilla, más las secciones complementarias 4.27–4.29 (matriz de alineación con la rúbrica, evidencias y checklist) y las tablas mínimas de gestión. 

**4.3 Planteamiento del problema** 

**4.3.1 Situación actual** 

La Intendencia de Montevideo atiende las incidencias de limpieza urbana a través de canales de comunicación que no fueron diseñados específicamente para ese fin. En el relevamiento inicial del equipo se identificaron las siguientes características de la situación actual: 

| Aspecto  | Situación actual detectada |
| :---- | ----- |
| Canales de reporte  | Teléfono, formularios web de carácter general, presencial y redes sociales |
| Precisión de la ubicación | Baja: el ciudadano describe la ubicación en texto libre, sin coordenadas |
| Evidencia  | No se exige ni se incorpora evidencia fotográfica de forma sistemática |
| Clasificación  | No uniforme: cada operador interpretaría el tipo de incidencia de manera distinta (supuesto a validar) |
| Registro  | No existe un repositorio único georreferenciado; la información se dispersa por canal |
| Derivación  | Manual y dependiente de la experiencia del operador que recibe el reporte |
| Análisis  | No se aprovecha la distribución geográfica para planificar la recolección |

**4.3.2 Consecuencias observadas** 

De la situación anterior se derivan las siguientes consecuencias: 

1\. **Información incompleta**: según el relevamiento inicial del equipo (supuesto a validar con datos de la IM), parte de los reportes carece de la ubicación y el detalle necesarios para actuar, lo que obliga a contactar nuevamente al vecino. 

2\. **Tiempos de derivación extensos**: al no existir una clasificación estandarizada, la incidencia puede recorrer varias áreas antes de llegar al equipo responsable. 

3\. **Imposibilidad de análisis geográfico**: sin coordenadas ni base centralizada no es posible generar mapas de calor ni detectar puntos críticos, y la planificación de la recolección se basa en la experiencia previa. 

4\. **Sin trazabilidad normalizada**: no se dispone de un historial único por incidencia que permita auditar tiempos de respuesta y calidad de servicio. 

**4.3.3 Problema a resolver** 

*La Intendencia de Montevideo no dispone de un sistema único, georreferenciado y con clasificación estandarizada para el registro, seguimiento y análisis de las incidencias de limpieza urbana, lo que afecta la eficiencia de la atención al ciudadano y la capacidad de planificación de la recolección.* 

4 / 33  
**4.3.4 Justificación** 

Abordar el problema es pertinente porque afecta directamente la **calidad de vida de los vecinos**, la **salud ambiental** de Montevideo y la **eficiencia operativa** de la IM. Desde el punto de vista técnico, el proyecto permite aplicar los contenidos de las siete dimensiones de la carrera: desarrollo web, modelado de base de datos espaciales, administración de sistemas POSIX y Microsoft, y diseño de redes segmentadas y seguras. Desde el punto de vista académico, constituye un caso integrador realista que combina software, datos, infraestructura y seguridad. 

**4.4 Objetivos del proyecto** 

**4.4.1 Objetivo general** 

Desarrollar un sistema web de registro, clasificación por categorías y gestión de incidencias de limpieza urbana de Montevideo (caso de estudio académico, sin vínculo formal con la Intendencia de Montevideo), basado en PHP 8.4 con Laravel 13 y PostgreSQL/PostGIS, que centralice los reportes ciudadanos georreferenciados y brinde a la Intendencia una herramienta de seguimiento y análisis territorial. 

**4.4.2 Objetivos específicos** 

Los objetivos específicos se formulan en formato **SMART** (específicos, medibles, alcanzables, relevantes y con plazo definido). 

**OE1. Registro ciudadano.** Implementar un formulario web de reporte de incidencias con descripción, categoría, ubicación geográfica (mapa interactivo o coordenadas) y evidencia fotográfica, disponible al cierre del Hito 2 (23/10/2026). 

**OE2. Gestión interna.** Implementar el panel interno de operadores con estados de incidencia (Pendiente, En proceso, Resuelto), asignación de responsables y registro de la respuesta al ciudadano, funcional para el Hito 2\. 

**OE3. Análisis territorial.** Generar visualizaciones geográficas (mapas de distribución y puntos críticos) sobre datos PostgreSQL/PostGIS, presentadas al cierre del Hito 2 e instaladas con el despliegue del Hito 3\. 

**OE4. Base de datos espacial.** Diseñar e implementar el modelo entidad-relación y el diccionario de datos con la extensión espacial PostGIS (campos de tipo *geometry/geography, índices espaciales GIST)*, con el script SQL de creación validado para el Hito 2\. 

**OE5. Infraestructura de red segura.** Diseñar e implementar la topología de red segmentada por VLAN (Administradores, Usuarios, Servidores) con DMZ para el servidor web MIRA, firewall Cisco ASA 5505, VPN IPsec y acceso POSIX/Windows administrado, simulada y documentada para el Hito 3\. 

**OE6. Entorno servidor.** Preparar los servicios de Active Directory, DNS, DHCP, PostgreSQL/PostGIS y el servidor web MIRA sobre el plan de direccionamiento establecido (VLAN 3 y DMZ), configurados y verificados en el Hito 3\. 

**OE7. Calidad.** Ejecutar casos de prueba (funcionales y de integración) definidos en la Tabla 7.4, con un porcentaje de casos aprobados de al menos el 90 % antes del Hito 3\. 

**OE8. Documentación.** Mantener el registro documental del proyecto según la metodología de trazabilidad de tareas (etiquetas T-XXX-HERRAMIENTA-VXX con historial de cambios y evidencias), con el documento integrador versionado en formato PDF en cada hito. 

5 / 33  
**4.4.3 Resultados medibles** 

| Hito  | Resultado esperado medible |
| :---- | :---- |
| H1  (18/09/2026) | Plan consolidado, relevamiento inicial, requisitos preliminares RF/RNF y diseños (red, base de datos). |
| H2  (23/10/2026) | Prototipo funcional con reporte ciudadano y panel interno en entorno de desarrollo. |
| H3  (20/11/2026) | Sistema integrado desplegado en la topología de red simulada, con pruebas aprobadas (≥ 90 %) y documentación final. |

**4.5 Alcance, exclusiones y limitaciones** 

**4.5.1 Alcance** 

El proyecto comprende las siguientes actividades y productos: 

**Formulario web de reporte ciudadano** con mapa interactivo, categorías de incidencia, descripción y carga de fotografía. 

**Panel interno de operadores** de la Intendencia de Montevideo, con gestión de estados de la incidencia (Pendiente → En proceso → Resuelto), asignación de responsables y comunicación al ciudadano. 

**Clasificación por categorías**: el ciudadano elige la categoría de una lista predefinida, cada categoría tiene asociada un área responsable y el administrador puede modificar la categoría y el área de cada incidencia. 

**Base de datos espacial** en PostgreSQL 16 con PostGIS: modelo entidad-relación, diccionario de datos, índices espaciales y consultas de análisis territorial. **Aplicación web** desarrollada en PHP 8.4 / Laravel 13, servida por Nginx y desplegada en contenedores Docker. 

**Servidor POSIX (Ubuntu Server 24.04)** para el soporte de la aplicación y sus servicios. **Servidores Microsoft (Server 2022\)** con Active Directory, DNS, DHCP e integración de servicios. 

**Topología de red** con VLAN 1 (Administradores), VLAN 2 (Usuarios), VLAN 3 (Servidores) y DMZ para el servidor web MIRA; firewall Cisco ASA 5505, router de borde con VPN IPsec, switch core Cisco 3560 y switches de acceso 2960\. 

**Documentación integrada** según la plantilla ITI V2.0, la metodología de trazabilidad de tareas y la rúbrica 2026\. 

**4.5.2 Exclusiones** 

Quedan **fuera del alcance** del proyecto: 

La integración con los sistemas internos existentes de la Intendencia (expedientes, trámites en línea) y el pago o gestión de multas. 

La implementación de aplicaciones móviles nativas (la interfaz será responsive, no nativa). 

El despliegue en producción física de la Intendencia: la infraestructura se despliega y verifica en un entorno de laboratorio/simulación académico. 

La incorporación de datos históricos de la Intendencia si no son provistos por la contraparte. 

La gestión de infracciones al Código de Convivencia vinculadas a los reportes. 

La integración del sistema con modelos de lenguaje (LLM) u otros servicios externos de inteligencia artificial para clasificar incidencias.

**4.5.3 Limitaciones** 

Los datos de la Intendencia de Montevideo se utilizan **solo en carácter académico** y, cuando no se dispone de información oficial, se documentan supuestos razonables que la contraparte académica debe validar. 

6 / 33

El alcance de las dimensiones de infraestructura está sujeto a la capacidad del laboratorio (licencias académicas y software de simulación como Packet Tracer). 

**4.6 Factibilidad y viabilidad** 

**4.6.1 Factibilidad técnica** 

**Desarrollo web**: el equipo dispone de experiencia con PHP, HTML, CSS, JavaScript y frameworks modernos; Laravel 13 con PHP 8.4 es una combinación estable y ampliamente documentada. 

**Datos espaciales**: PostgreSQL 16 con PostGIS permite incorporar georreferenciación sin costo de licencia y con documentación suficiente para el modelado espacial.

**Infraestructura**: la topología (Cisco ASA 5505, 3560, 2960\) se simuló y documentó con Cisco Packet Tracer y se contrasta con la práctica de laboratorio; los servidores POSIX y Windows se configuran sobre máquinas virtuales. 

**Contenedores**: Docker garantiza replicabilidad del entorno de aplicación en cualquier equipo del equipo. 

**Conclusión técnica**: el proyecto es factible con el conocimiento actual del equipo y las tecnologías académicas disponibles. 

**4.6.2 Factibilidad operativa** 

El cronograma (H1 el 18/09, H2 el 23/10, H3 el 20/11) respeta los tiempos académicos; cada tutoría es un punto de control de avance. 

Las tareas se distribuyen según las áreas funcionales del equipo (DFB, IRS, GPD, CTA) definidas en la sección 4.10. 

La documentación sigue la metodología de trazabilidad de tareas propuesta por la tutoría de Gestión de Registro Documental, con etiquetas estandarizadas (T-XXX-ÁREA VXX). 

**4.6.3 Factibilidad económica** 

No se requieren compras de software: **todas las herramientas son de licencia gratuita o académica**. 

| Componente  | Licencia  | Costo |
| :---- | :---- | :---- |
| PHP 8.4, Laravel 13, Nginx  | Libre / Open Source  | $ 0 |
| PostgreSQL 16 \+ PostGIS  | PostgreSQL License  | $ 0 |
| Docker Desktop  | Gratuita (uso individual)  | $ 0 |
| Windows Server 2022, Windows 11 | Evaluación académica / laboratorio  | $ 0 |
| Cisco ASA 5505, 3560, 2960  | Emulación Cisco Packet Tracer  | $ 0 |
| VS Code, Git, Inkscape, Miro, Trello | Gratuitas  | $ 0 |

7 / 33  
**Conclusión económica**: el proyecto no representa costos significativos; no hay costos de licencias ni de servicios externos. 

**4.6.4 Viabilidad legal y ética** 

Los datos de la Intendencia de Montevideo se tratan en carácter académico y anonimizados cuando corresponda. 

No se utilizan imágenes ni marcas oficiales fuera del contexto académico sin autorización de la contraparte.

Los datos personales de los usuarios registrados y las fotos de las incidencias se guardan en el servidor del proyecto (no se envían a servicios externos), el mapa público no muestra datos personales y todo se trata conforme a la Ley N.º 18.331 (2008). 

**4.7 Análisis FODA y gestión de riesgos** 

**4.7.1 Análisis FODA** 

**Fortalezas (internas)** 

Equipo multidisciplinario con áreas funcionales definidas (DFB, IRS, GPD, CTA). Utilización de tecnologías modernas y de bajo costo (Laravel, PostgreSQL/PostGIS, Docker). 

Canal de comunicación fluido entre integrantes y con el tutor. 

Esquema de red ya consolidado y aprobado en etapa de diseño. 

**Oportunidades (externas)** 

Necesidad real de mejora en la atención de incidencias de limpieza urbana en Montevideo. 

Aplicación directa de las siete dimensiones de la carrera, lo que refuerza el aprendizaje. Posibilidad de utilizar la solución como caso de referencia de gestión municipal. 

**Debilidades (internas)** 

Carga horaria limitada de los integrantes. 

Curva de aprendizaje de PostGIS para parte del equipo.

 Dependencia del acceso a laboratorio para dimensiones de infraestructura/seguridad. 

**Amenazas (externas)** 

Cambios en las plataformas académicas o en los tiempos de los tutores.  
Dificultad para acceder a datos reales de la Intendencia de Montevideo. Disponibilidad limitada de equipos (ASA 5505, switches) fuera del simulador. 

**4.7.2 Gestión de riesgos** 

La siguiente matriz resume los riesgos identificados, su probabilidad, impacto y plan de mitigación. Se detalla en la **Tabla 7.2** del presente documento. 

| ID  | Riesgo  | Probabilidad  | Impacto  | Mitigación |
| ----- | :---- | ----- | ----- | :---- |
| R01  | Atraso en el cronograma por falta de tiempo de los integrantes | Media  | Alto  | Planificación semanal en Scrum, ajuste de alcance por sprint, entregas parciales |

8 / 33

| ID  | Riesgo  | Probabilidad  | Impacto  | Mitigación |
| ----- | :---- | ----- | ----- | :---- |
| R02  | Documentación  descentralizada o sin trazabilidad | Media  | Alto  | Metodología de trazabilidad de tareas T-XXX; reunión de revisión documental por sprint |
| R03  | Dificultad para modelar datos espaciales en PostGIS | Media  | Medio  | Capacitación con tutoriales, apoyo del tutor de Base de Datos, prototipo temprano |
| R04  | No obtener datos reales de la Intendencia | Alta  | Medio  | Uso de datos sintéticos representativos y supuestos documentados |
| R05  | Fallas de red/laboratorio para simulaciones | Media  | Medio  | Uso de Packet Tracer como respaldo y verificación en laboratorio cuando esté disponible |
| R06  | Riesgo de integridad de datos (reportes ciudadanos) | Baja  | Alto  | Transaccionalidad en PostgreSQL, backups y control de acceso por roles |

**4.8 Entregables e hitos** 

**4.8.1 Hitos** 

| Hito  | Fecha  | Descripción |
| ----- | ----- | :---- |
| Hito  1 | 18/09/2026  | Documento integrador V1.0 con plan de proyecto, relevamiento inicial, requisitos preliminares (RF/RNF), FODA y riesgos, y diseños de red, base de datos y registros documentales. |
| Hito  2 | 23/10/2026  | Prototipo funcional MIRA en entorno de desarrollo: reporte ciudadano, panel interno y consultas espaciales; actualización del documento integrador. |
| Hito  3 | 20/11/2026  | Sistema integrado desplegado en la topología simulada, pruebas de aceptación aprobadas, documentación completa y defensa final. |

**4.8.2 Entregables** 

Los entregables se detallan en la **Tabla 7.1** del presente documento. A modo de resumen: 

| Área  | Entregable  | Estado H1 |
| ----- | :---- | :---- |
| GPD  | Documento integrador ITI versionado (PDF)  | En producción |
| GPD  | Cronograma, actas y registro de riesgos  | En producción |
| GPD  | Plan de trazabilidad de tareas (etiquetas T-XXX)  | En producción |
| CTA  | Requisitos RF/RNF preliminares y casos de prueba  | En producción |
| CTA  | Diagramas de análisis y diseño (CASO, UML, MER)  | En producción |
| DFB  | Diseño de arquitectura web y prototipo de interfaces  | Borrador |
| DFB  | Código fuente del módulo de reporte (sprint 1\)  | Pendiente |
| CTA  | Script SQL de base de datos con PostGIS  | Borrador |
| IRS  | Topología, direccionamiento VLAN y políticas de seguridad  | En producción |
| IRS  | Configuración de servidores POSIX/Windows y servicios de red  | Borrador |

9 / 33  
**4.8.3 Registro documental** 

Para cada tarea se registra una **etiqueta de trazabilidad** conforme a la metodología de la tutoría de Gestión de Registro Documental, con el formato: 

T-XXXX-ÁREA-VXX 

Ejemplo del ciclo de vida de una tarea: 

T-001-DFB-V0.1 | DIARIO | EN PRODUCCIÓN 

T-001-DFB-V0.2 | GITHUB | EN DESARROLLO 

T-001-TEST-V0.3 | TESTING | EN REVISIÓN 

T-001-GPD-V1.0 | PROD | APROBADO 

El registro completo por tarea (responsable, fecha, cambio, motivo, resultado, evidencia, revisado por) se conserva en el historial de tareas del equipo, incluido en el Anexo documental. 

**4.9 Planificación y seguimiento** 

**4.9.1 Cronograma general** 

El proyecto se planifica en tres fases alineadas con los hitos y con una duración aproximada de 12 semanas entre el 02/09/2026 y el 20/11/2026. Las fechas de inicio de cada semana son orientativas: los eventos Scrum se organizan de lunes a viernes (ver 4.11.1). 

| Semana  | Fecha  inicio | Fase  | Actividad principal  | Responsable |
| ----- | ----- | ----- | :---- | ----- |
| S1  | 02/09/2026  | Inicio  | Kick-off, definición de roles y metodología, estudio de la rúbrica | GPD |
| S2  | 09/09/2026  | Inicio  | Relevamiento inicial de canales; definición del alcance | CTA \+ GPD |
| S3  | 16/09/2026  | Inicio  | Cierre del documento integrador; **entrega Hito 1** | Todo el equipo |
| S4  | 23/09/2026  | Desarrollo  | Modelo de datos y script SQL (PostGIS)  | CTA |
| S5  | 30/09/2026  | Desarrollo  | Autenticación, roles y módulo de reporte ciudadano | DFB |
| S6  | 07/10/2026  | Desarrollo  | Mapa interactivo y carga de imágenes; gestión de categorías y áreas | DFB |
| S7  | 14/10/2026  | Desarrollo  | Panel interno y consultas espaciales; casos de prueba | DFB \+ CTA |
| S8  | 21/10/2026  | Integración  | **Entrega Hito 2** (prototipo funcional)  | Todo el equipo |
| S9  | 28/10/2026  | Infraestructura  | Servidores POSIX/Windows y servicios (DNS, DHCP, AD) | IRS |
| S10  | 04/11/2026  | Infraestructura  | Topología de red: VLAN, firewall, DMZ y VPN | IRS |
| S11  | 11/11/2026  | Cierre  | Despliegue integrado, pruebas de aceptación y documentación final | Todo el equipo |
| S12  | 18/11/2026  | Cierre  | **Entrega Hito 3** y preparación de la presentación | GPD |

10 / 33  
**4.9.2 Seguimiento** 

**Sprint semanal** de Scrum: planificación el primer día hábil y revisión/retrospectiva el último día hábil de cada semana. 

**Daily breve (sincrónica por Meet o asincrónica por WhatsApp)** para destrabar bloqueos. **Revisión documental** en cada sprint con la metodología de trazabilidad de tareas. La **matriz de riesgos** (Tabla 7.2) se revisa en cada sprint y se actualiza la probabilidad/impacto cuando cambian las condiciones. 

Los avances se verifican contra la **rúbrica 2026** antes de cada entrega. **4.10 Organización y recursos** 

**4.10.1 Equipo del proyecto** 

El equipo **Bit Oriental** (grupo TC4) está integrado por tres estudiantes organizados en un esquema matricial de áreas funcionales: 

| Integrante  | Rol principal  | Áreas funcionales |
| ----- | :---- | :---- |
| Thomas Galo  | Coordinador / Product Owner (GPD) | Gestión de proyecto y documentación, calidad y análisis (UML, MER, pruebas) |
| Fabio  Fernández | Subcoordinador / Scrum Master (DFB) | Desarrollo frontend/backend (PHP, Laravel, Docker), diseño |
| Riccardo  Inojosa | Desarrollador de infraestructura (IRS) | Infraestructura, redes, seguridad, POSIX, Redes Microsoft |

**4.10.2 Matriz de áreas funcionales** 

| Área  | Sigla  | Responsable  | Responsabilidades |
| ----- | :---: | :---- | :---- |
| Desarrollo  Frontend/Backend | DFB  | Fabio Fernández  | Código PHP/Laravel, interfaces, API, Docker |
| Infraestructura, Redes y Seguridad | IRS  | Riccardo Inojosa  | Topología, VLAN, firewall, servidores POSIX/Windows, seguridad |
| Gestión de Proyecto y Documentación | GPD  | Thomas Galo  | Cronograma, riesgos, actas, anteproyecto, documento integrador |
| Calidad, Testing y Análisis  | CTA  | Thomas Galo (apoyo de todo el equipo) | UML, MER, diccionario de datos, casos de prueba |

*Nota: el área CTA se apoya en revisión cruzada de todo el equipo; las definiciones de análisis recaen principalmente en GPD con la validación técnica de DFB e IRS.* 

**4.10.3 Tutores y vínculo con las dimensiones** 

| Dimensión (rúbrica)  | Tutor |
| :---- | :---- |
| I. Gestión de Registro Documental  | Martín Viar |
| II. Gestión de Proyecto  | Martín Viar |
| III. Tutoría de Programación  | Christian Menéndez |
| IV. Tutoría de Base de Datos  | Julio González |
| V. Tutoría de POSIX / VI. Redes MS  | Gerson Rodríguez |
| VII. Conectividad y Seguridad de Redes  | Mario Malagrino |
| Pruebas / Calidad  | Carina Sindoni |

11 / 33  
**4.10.4 Recursos** 

**Software y herramientas**: ver sección 4.12 (Miro, Trello, GitHub, ChatGPT/OpenAI, Packet Tracer, VirtualBox, Docker, VS Code). 

**Hardware de laboratorio**: equipos de los integrantes y máquinas virtuales; emulador Cisco Packet Tracer para los dispositivos de red. 

**Canal de comunicación**: WhatsApp y Meet; Drive compartido PROYECTO MIRA para la documentación oficial. 

**4.11 Metodología de trabajo** 

**4.11.1 Marco de trabajo: Scrum** 

El equipo trabaja con Scrum (Schwaber y Sutherland, 2020\) adaptado al contexto académico: 

**Sprints**: ciclos semanales con un objetivo (Sprint Goal) definido. 

**Roles en el proyecto**: Thomas Galo (Product Owner: prioriza backlog y valida entregables del proyecto), Fabio Fernández (Scrum Master: facilita el proceso y destraba impedimentos) y Riccardo Inojosa (Developer: implementa infraestructura). Todos los integrantes forman el Developer Team y desarrollan el backlog según sus áreas funcionales; además, Thomas cumple el rol de Product Owner y Fabio el de Scrum Master. 

**Artefactos**: Product Backlog (requisitos priorizados), Sprint Backlog (tareas de la semana) y el incremento del proyecto (documento integrador \+ código \+ configuraciones). 

**Eventos**: Sprint Planning (lunes), Daily (breve, asincrónico), Sprint Review (viernes, con el tutor) y Sprint Retrospective. 

**4.11.2 Backlog del proyecto (resumen)** 

| Prioridad  | Épica  | Ítem de backlog de alto nivel  | Sprint |
| ----- | ----- | :---- | ----- |
| Alta  | Registro ciudadano  | Formulario de reporte con mapa, categorías y archivos  | S5-S6 |
| Alta  | Categorías y áreas | Categorías con área asociada y cambio de categoría/área por el administrador  | S6 |
| Alta  | Panel interno  | Gestión de estados, asignación y comunicación al vecino  | S7 |
| Media  | Análisis territorial  | Mapas de distribución y puntos críticos (PostGIS)  | S7 |
| Alta  | Infraestructura  | Red segmentada por VLAN \+ DMZ y servidores  | S9-S10 |
| Media  | Calidad  | Casos de prueba funcionales y de integración  | S7-S11 |

**4.11.3 Estrategia de desarrollo (disciplinada)** 

**Git \+ GitHub** para el control de versiones del código, con commits por tarea (T-XXX ÁREA-VXX) y ramas por funcionalidad. 

**Entornos**: desarrollo local (Docker) → integración (VMs) → simulación de red. **Convención de ramas**: main (estable) y feature/T-XXX-... (funcionalidades en desarrollo). 

Las **decisiones de diseño** se aprueban en la Sprint Review y se registran en el historial de tareas (Anexo A). 

**4.11.4 Metodología de documentación (trazabilidad)** 

Siguiendo la guía de la Tutoría de Gestión de Registro Documental, toda tarea lleva una **etiqueta de trazabilidad** con su ciclo de vida (Análisis → Planificación → Desarrollo → BD → Testing → Corrección → Producción), tal como se describe en la sección 4.8.3 y en el Anexo A. 

12 / 33  
**4.12 Procedimientos, herramientas y tecnología** 

**4.12.1 Herramientas de gestión y colaboración** 

| Herramienta  | Uso  | Evidencia registrada |
| :---- | ----- | :---- |
| Miro  | Análisis y diseño: escenarios, flujos, wireframes, UML | Capturas/enlaces en el historial de tareas |
| Trello  | Gestión de tareas: Backlog → … → Aprobado  | Enlace a tarjetas (T-XXX) |
| GitHub  | Control de versiones del código  | Commits, issues y ramas |
| Google Drive  | Repositorio documental oficial PROYECTO MIRA | Actas, borradores, entregables |
| ChatGPT / OpenAI | Apoyo al equipo en redacción, revisión de código y documentación (herramienta de trabajo; no integrada al sistema, ver 4.5.2) | Registro en el historial de tareas |
| WhatsApp / Meet | Comunicación del equipo y tutorías  | Registro en actas |

**4.12.2 Stack tecnológico de la solución** 

| Capa  | Tecnología  | Versión |
| :---- | :---- | ----- |
| Frontend  | HTML5, CSS3, JavaScript, Bootstrap  | — |
| Backend  | PHP \+ Laravel  | PHP 8.4 / Laravel 13 |
| Servidor web  | Nginx (proxy \+ estáticos)  | — |
| Base de datos  | PostgreSQL \+ PostGIS  | 16 |
| Mapa  | Leaflet (OSM) / Google Maps (según licencia académica)  | — |
| Contenedores  | Docker / Docker Compose  | — |
| Virtualización  | VirtualBox / Hyper-V (laboratorio)  | — |

**4.12.3 Herramientas de infraestructura** 

| Herramienta  | Uso |
| :---- | :---- |
| Cisco Packet Tracer  | Simulación de la topología. Equipamiento emulado: firewall Cisco ASA 5505 (perimetral), switch multicapa Cisco 3560 (core, enrutamiento inter-VLAN) y switches Cisco 2960 (acceso). Se usa el ASA 5505 por estar disponible en Packet Tracer; en un despliegue real se sustituiría por un Firepower/ASA vigente. El ASA 5505 con licencia Base solo admite 3 VLAN y restringe la DMZ: para usar las interfaces OUTSIDE, INSIDE y DMZ sin restricciones se asume la licencia Security Plus. |
| VirtualBox / Hyper-V / UTM | Servidores POSIX y Windows en máquinas virtuales |
| Wireshark  | Análisis de tráfico y verificación de política de firewall |

**4.12.4 Procedimiento de trabajo** 

1\. **Registrar la tarea** en Trello con el código de trazabilidad (T-XXX-ÁREA-VXX). 2\. **Diseñar/esquematizar** en Miro cuando la tarea lo requiera.   
3\. **Implementar** en GitHub con un commit descriptivo (evidencia: hash del commit). 4\. **Pasar a testing** con el caso de prueba asociado (evidencia: captura o reporte). 5\. **Registrar el resultado** y cerrar la tarea en Trello con estado "Aprobado". 6\. **Documentar** el resultado en el Drive oficial y actualizar el documento integrador. 

**4.13 Relevamiento y requisitos** 

**4.13.1 Estrategia de relevamiento** 

El relevamiento se realizó en las primeras dos semanas del proyecto mediante: 13 / 33  
**Análisis de los canales de comunicación actuales** de la Intendencia de Montevideo (fuentes públicas: sitio web, teléfono de la IM, formularios de consultas generales). **Cuestionario** dirigido a los ciudadanos de Montevideo en formato google forms. 

**4.13.2 Hallazgos del relevamiento** 

1\. Los **canales actuales** son generalistas (teléfono y formulario web), sin un flujo específico de incidencias de limpieza. 

2\. Existen **tipos de incidencia** habituales: contenedor desbordado, residuos fuera de contenedor, acumulación de basura, contenedor fuera de lugar, ramas/malezas, camada ilegal. 

3\. Los ciudadanos esperan **seguimiento** del reporte (estado y respuesta). 4\. La Intendencia necesita **datos geográficos** para planificar la recolección (puntos críticos). 

5\. Se requiere **control de acceso por roles** (ciudadano, inspector/operador, administrador). 

**4.13.3 Usuarios y roles** 

| Rol  | Descripción  | Permisos principales |
| :---- | ----- | :---- |
| Visitante (no registrado) | Persona que consulta el mapa público | Ver el mapa con las ubicaciones de las incidencias pendientes (sin datos personales). No puede reportar ni hacer seguimiento. |
| Ciudadano registrado  | Vecino con cuenta  | Crear reportes (con fotografías), seguimiento del estado e historial de sus reportes, además de ver el mapa |
| Inspector / operador IM  | Personal que gestiona incidencias | Ver listado/mapa, asignar, cambiar estado (Pendiente → En proceso → Resuelto), responder al ciudadano |
| Administrador  | Gestión del sistema  | Gestión de usuarios/áreas, configuración de categorías, reportes estadísticos, logs |

**4.13.4 Requisitos funcionales (RF) — preliminares** 

La documentación completa se encuentra en la **Tabla 7.3** (requisitos RF/RNF). Resumen: 

**RF01** Registrar una incidencia con: descripción, categoría, ubicación (mapa o coordenadas), foto opcional. Solo usuarios registrados y autenticados pueden reportar; los datos de contacto se toman de su cuenta. 

**RF02** Asociar a cada incidencia un **código único** de seguimiento, que el ciudadano registrado consulta desde su cuenta. 

**RF03** Geo-referenciar coordenadas en el mapa y almacenarlas como geometría PostGIS. 

**RF04** Categorización: el ciudadano elige la categoría (que tiene un área responsable asociada) y el administrador puede cambiar la categoría y el área. **RF05** Panel de operadores: listado, filtros por estado/área/ubicación, detalle y edición. **RF06** Gestión de estados del reclamo: Pendiente → En proceso → Resuelto (estados canónicos). 

**RF07** Asignación de responsable y registro del cuaderno de trabajo (historial de la incidencia). 

**RF08** Notificación al ciudadano cuando cambia el estado (por correo o desde la sección de seguimiento de su cuenta). 

14 / 33  
**RF09** Visualización geográfica: mapa de incidencias, filtro por categoría y estado, identificación de puntos críticos. El mapa es público: los visitantes no registrados ven las ubicaciones de las incidencias pendientes, sin datos personales. 

**RF10** Gestión de usuarios y roles (registro, login, roles, permisos). 

**RF11** Reportes estadísticos (cantidad por categoría/estado/barrio/período). **RF12** Exportación de datos (CSV) para análisis. 

**4.13.5 Requisitos no funcionales (RNF) — preliminares** 

**RNF01 Rendimiento**: respuesta de páginas del panel ≤ 3 s con hasta 200 usuarios simultáneos. 

**RNF02 Escalabilidad**: arquitectura de contenedores que permita replicar servicios. **RNF03 Seguridad (acceso)**: autenticación por sesión/BCrypt, protección CSRF, RBAC por roles. 

**RNF04 Seguridad (red)**: el panel interno solo es accesible desde las VLAN internas (Usuarios y Administración) y no se publica a Internet; el servidor web publicado solo en DMZ (puerto 80/443). El panel interno sólo es accesible desde las VLAN internas (Usuarios y Administración); no se publica a Internet 

**RNF05 Disponibilidad**: backups de la base de datos (programación diaria) y alta de servicios con Docker Compose. 

**RNF06 Usabilidad**: interfaz responsive (ciudadano en móvil), mapa legible en smartphone. 

**RNF07 Compatibilidad**: navegadores modernos (Chrome, Edge, Firefox; Safari en móvil). 

**RNF08 Integridad de datos**: constraint de integridad referencial, índices espaciales y transaccionalidad. 

**RNF09 Accesibilidad**: contraste y etiquetas mínimos (WCAG A) en formularios. **RNF10 Legal**: cumplimiento de la Ley 18.331 (protección de datos personales). 

**4.14 Análisis y diseño** 

**4.14.1 Caso de uso principal (resumen)** 

**Caso de uso "Reportar incidencia"** (CU-01) 

| Campo  | Detalle |
| ----- | :---- |
| Actor  | Ciudadano registrado |
| Disparador  | El ciudadano detecta una incidencia de limpieza |
| Precondición  | Usuario registrado y autenticado; conexión a internet; navegador móvil/escritorio. |
| Flujo básico  | 1\. El ciudadano abre el formulario de reporte. 2\. Describe la incidencia. 3\. Selecciona la categoría de la lista. 4\. Marca la ubicación en el mapa (o usa geolocalización). 5\. Opcionalmente adjunta foto. 6\. Confirma sus datos de contacto (tomados de su cuenta). 7\. Envía. |
| Postcondición  | El sistema crea la incidencia con código único; se le asigna el área asociada a la categoría; el estado inicial es *Pendiente*. |
| Requisitos  | RF01-RF04 |

**Caso de uso "Gestionar incidencia"** (CU-02) 

| Campo  | Detalle |
| ----- | :---- |
| Actor  | Inspector / operador IM (autenticado) |
| Flujo básico  | 1\. Accede al panel. 2\. Filtra el listado o el mapa. 3\. Abre la incidencia. 4\. Asigna responsable. 5\. Cambia estado (Pendiente → En proceso → Resuelto). 6\. Registra la respuesta. 7\. El sistema notifica al ciudadano. |
| Postcondición  | Historial de la incidencia actualizado. |

15 / 33

| Campo  | Detalle |
| :---- | :---- |
| Requisitos  | RF05-RF08 |

*(El conjunto completo de casos de uso se detalla en el Anexo B junto con el diagrama de casos de uso.)* 

**4.14.2 Diagrama de casos de uso (descriptivo)** 

El diagrama incluye los actores Visitante, Ciudadano registrado, **Inspector/Operador IM** y **Administrador**, con los casos de uso: *Registrar incidencia*, *Ver mapa público, Seguir mis reportes*, *Autenticarse, Cambiar categoría y área*, *Gestionar incidencia*, *Asignar responsable*, *Administrar usuarios/categorías*, *Generar reportes* y *Exportar datos*. 

**4.14.3 Modelo conceptual de datos (MER)** 

El modelo entidad-relación del sistema se describe según los siguientes criterios generales: 

**Usuario**: personas con roles (ciudadano, operador, admin). Tabla usuario con discriminante de rol. 

**Incidencia**: reporte ciudadano con descripción, categoría, geometría (espacial), fotografías y estado. Tabla incidencia. 

**Historial / cuaderno de trabajo**: cambios de estado y comentarios. Tabla historial\_incidencia. 

**Categoría**: clasificación de incidencia (tabla categoria). 

**Área responsable**: unidades de la IM para la derivación (tabla area). 

**Asignación**: relación *Incidencia–Área–Usuario* (responsable) con fecha. 

El detalle completo (entidades, atributos, claves, cardinalidades, normalización) y el diccionario de datos se desarrollan en la sección 4.16 (Base de datos) y en el Anexo C. 

**4.14.4 Arquitectura de la solución** 

**Arquitectura por capas**: presentación (Vistas Blade/JS) → aplicación (Laravel: controladores, servicios) → datos (Eloquent \+ SQL/PostGIS). 

**Mapa**: librería Leaflet sobre tiles de OpenStreetMap; el backend expone endpoints GeoJSON consumidos por el mapa. 

**Despliegue**: contenedores Docker (app, nginx, db/base de datos, queue opcional) orquestados con Docker Compose. 

**4.14.5 Prototipos preliminares (wireframes)** 

Los wireframes de baja fidelidad se mantienen en el tablero Miro del proyecto (enlace en el Anexo D). Incluyen: formulario de reporte móvil, panel de operadores con mapa, detalle de incidencia y vista de estadísticas. 

**4.15 Desarrollo e implementación de software** 

**4.15.1 Estrategia de desarrollo** 

El desarrollo sigue un enfoque incremental con entregas por Sprint (ver cronograma), sobre el framework Laravel 13 (Laravel, 2026). En el Hito 1 se definen la arquitectura y las pautas de codificación; el prototipo funcional se entrega en el Hito 2\. 

16 / 33  
**4.15.2 Estructura del proyecto Laravel 13** 

| Ruta  | Contenido |
| :---- | ----- |
| routes/web.php,  routes/api.php | Rutas web y endpoints JSON (mapa, categorías) |
| app/Models  | Modelos Eloquent: User, Incidencia, Categoria, Area, HistorialIncidencia |
| app/Services  | Servicios: IncidenciaService, NotificacionService |
| app/Http/Controllers  | Controladores por módulo (Auth, Incidencia, Panel, Admin) |
| database/migrations  | Migraciones (usuarios, categorías, áreas, incidencias, historial) |
| database/seeders  | Datos iniciales (categorías, áreas, usuarios demo) |
| resources/views  | Vistas Blade: público (reporte) y panel (operador/admin) |
| public/map  | Recursos del mapa Leaflet |

**4.15.3 Módulos principales** 

**Módulo público (ciudadano)** 

Formulario de reporte con Leaflet (clic en mapa \+ geolocalización opcional). Validación de descripción, categoría (lista predefinida) y ubicación. Carga de imagen con previsualización (máx. 5 MB) y datos EXIF de geolocalización. Pantalla de "incidencia registrada" con código único de seguimiento. 

**Módulo de categorías y áreas** 

Catálogo de categorías de incidencia, cada una con su área responsable asociada. El ciudadano elige la categoría al reportar y el sistema asigna el área correspondiente; el administrador puede cambiar la categoría y el área de una incidencia, y el cambio queda registrado en el historial.

**Módulo interno (operador/admin)** 

Listado con filtros (estado, categoría, área, período) y vista mapa. 

Detalle de incidencia con historial (cuaderno de trabajo) y acción de cambio de estado. Portal de estadísticas (agrupaciones por categoría/estado/distrito/período) y exportación CSV. 

**4.15.4 API interna** 

| Método  | Ruta  | Descripción |
| ----- | :---- | :---- |
| POST  | /api/incidencias  | Crear incidencia (validación \+ geocodificación) — solo usuarios registrados y autenticados |
| GET  | /api/incidencias.geojson  | Incidencias para el mapa público: solo pendientes y sin datos personales (filtros opcionales) |
| GET  | /api/incidencias/{id}  | Detalle de la incidencia (solo el ciudadano que la reportó u operadores, autenticados) |
| PATCH | /api/incidencias/{id}/categoria | Cambio de categoría y área (administrador) |
| PATCH  | /api/incidencias/{id}/estado  | Cambio de estado (operador) |
| GET  | /api/estadisticas  | Agregaciones para reportes |

**4.15.5 Pautas de calidad del código** 

PSR-12, rutas agrupadas por middleware de rol, mensajes de error en español. 17 / 33  
Migraciones versionadas; *seeders* para datos de prueba; *feature tests* de Laravel para los casos de la Tabla 7.4. 

Commits con código de trazabilidad (T-XXX-DFB-VXX) en ramas feature/. **4.16 Base de datos** 

**4.16.1 Motor y extensión espacial** 

Se utiliza PostgreSQL 16 (PostgreSQL Global Development Group, 2026\) con PostGIS (OSGeo, 2026), motor relacional de código abierto. PostGIS agrega el tipo de datos espaciales geometry, funciones geoespaciales (distancia, área, union, centroide) e **índices espaciales (GIST)**, requisito fundamental para el análisis territorial de las incidencias. 

**4.16.2 Modelo entidad-relación (MER)** 

| Entidad  | Descripción  | Relaciones |
| ----- | :---- | :---- |
| usuario  | Personas del sistema (ciudadano, operador, admin) | 1:N con incidencia (creador), N:M con area (asignación) |
| categoria  | Tipo de incidencia (contenedor desbordado, residuos, etc.) | 1:N con incidencia |
| area  | Área responsable de la IM  | N:M con usuario (responsables) |
| incidencia  | Reporte ciudadano con datos espaciales | N:1 categoria, N:1 usuario, 1:N con historial\_incidencia |
| historial\_incidencia  | Cuaderno de trabajo (cambios de estado, comentarios) | N:1 incidencia, N:1 usuario |

**4.16.3 Diccionario de datos (resumen)** 

**Tabla incidencia** (Base de datos espacial) 

| Campo  | Tipo  | Descripción |
| ----- | ----- | :---- |
| id  | BIGSERIAL PK  | Identificador único |
| codigo  | VARCHAR(20) UNIQUE  | Código de seguimiento de la incidencia (ej. MIR-2026-000123) |
| categoria\_id  | BIGINT FK  | Categoría de la incidencia |
| ciudadano\_id  | BIGINT FK  | Usuario registrado que reporta (NOT NULL: solo usuarios registrados pueden reportar) |
| descripcion  | TEXT  | Descripción libre |
| geom  | GEOMETRY(Point, 4326\)  | Coordenadas geográficas (WGS84) |
| foto\_url  | VARCHAR(255)  | Ruta de la imagen (almacenada en storage) |
| estado  | VARCHAR(15)  | Pendiente / En proceso / Resuelto |
| area\_id | BIGINT FK  | Área responsable (se toma de la categoría; el administrador puede cambiarla) |
| creado\_en  | TIMESTAMPTZ  | Fecha de creación |
| resuelto\_en  | TIMESTAMPTZ  | Fecha de resolución (nullable) |

**Tabla historial\_incidencia** 

18 / 33

| Campo  | Tipo  | Descripción |
| :---- | :---- | :---- |
| id  | BIGSERIAL PK  | Identificador |
| incidencia\_id  | BIGINT FK  | Incidencia asociada |
| usuario\_id  | BIGINT FK  | Quién realizó el cambio |
| estado\_anterior / estado\_nuevo  | VARCHAR(15)  | Cambio de estado |
| comentario  | TEXT  | Nota del operador |
| creado\_en  | TIMESTAMPTZ  | Timestamp |

*(El diccionario completo de todas las tablas se incluye en el Anexo C.)* 

**4.16.4 Consultas espaciales de análisis** 

**Puntos críticos**: agrupación por ST\_ClusterWithin(geom, radio) o agrupación por distrito con conteo de incidencias. 

**Incidencias en radio**: ST\_DWithin(geom, punto, radio). 

**Conteo por barrio/municipio: uso de los límites administrativos y ST\_Contains(limite.geom, incidencia.geom).** 

**Índices espaciales**: GIST sobre geom (obligatorio) y B-tree sobre estado, categoria\_id, creado\_en. 

**4.16.5 Scripts y migración** 

Migraciones Laravel con campos geometry vía PostGIS (DB::statement para índices GIST). 

*Seeder* con categorías y áreas de ejemplo. 

**Respaldo**: pg\_dump programado (cron) y restauración verificada (Hito 3). 

**4.16.6 Verificación de integridad** 

Constraints CHECK en estado (Pendiente/En proceso/Resuelto). 

Claves foráneas con ON DELETE RESTRICT en historial\_incidencia.incidencia\_id. Auditoría vía *trigger* de actualización de resuelto\_en cuando se llega a Resuelto. 

**4.17 POSIX (servidor Linux)** 

**4.17.1 Rol en el proyecto** 

El servidor **Ubuntu Server 24.04 LTS (SRV-POSIX, 10.10.30.14, VLAN Servidores)** aloja los servicios de la aplicación y facilita la administración centralizada mediante herramientas POSIX. 

**4.17.2 Servicios del servidor POSIX** 

| Servicio  | Tecnología  | Descripción |
| :---- | :---- | :---- |
| Motor de aplicación | Docker \+ Docker Compose | Contenedores de la app Laravel, Nginx y PostgreSQL/PostGIS |
| Base de datos  | PostgreSQL 16 \+ PostGIS | Datos espaciales del sistema MIRA |
| Servidor web  | Nginx  | Proxy reverso y estáticos (puerto 80/443) |
| Servidor de archivos | NFS / rsync  | respaldo y sincronización (opcional) |

19 / 33

| Servicio  | Tecnología  | Descripción |
| :---- | :---- | :---- |
| Respaldo  | cron \+ pg\_dump  | Backup diario de la base de datos |

**4.17.3 Usuarios y permisos** 

Usuario de administración admin con sudo (acceso solo por VLAN 1/enfoque de administración). 

Usuario de aplicación mira-app sin shell interactivo (nologin), dueño del directorio de la aplicación. 

Grupos: admins, deployers. SSH con conexión por llave y deshabilitado el login de root por contraseña. 

**4.17.4 Seguridad del sistema** 

**SSH**: puerto no estándar opcional, llaves RSA/Ed25519, deshabilitado PermitRootLogin, Fail2ban. 

**Firewall: ufw permitiendo 443 solo desde 10.10.40.10 (DMZ) y desde la VLAN de usuarios; 22 solo desde la VLAN de administración (VLAN 1); 5432 solo desde localhost/red Docker.** 

**Actualizaciones**: unattended-upgrades para parches de seguridad. 

**Auditoría**: journalctl y archivos de log centralizados. 

**Backups**: plan de respaldo (diario full, retención 7 días) y restauración probada. 

**4.17.5 Comandos relevantes de verificación** 

docker compose ps y docker compose logs \-f 

systemctl status nginx postgresql docker 

ss \-tlnp para verificar puertos escuchando 

pg\_stat\_activity y EXPLAIN para consultas espaciales 

psql: consultas espaciales de prueba (ver sección 4.16.4) 

**4.18 Redes Microsoft** 

**4.18.1 Rol en el proyecto** 

El entorno Microsoft (Windows Server 2022\) aporta la **infraestructura de identidad y servicios de red**: Active Directory Domain Services, DNS y DHCP. El servidor **SRV-AD-DNS (10.10.30.13, VLAN Servidores)** es el controlador de dominio del proyecto y publica los servicios de nombre y direccionamiento del dominio mira.local. 

**4.18.2 Servicios y configuración** 

| Servicio  | Función  | Detalles |
| :---- | ----- | :---- |
| Active Directory Domain Services | Dominio  mira.local | Bosque/dominio único; OUs por área (ADMIN, OPERADORES, CIUDADANOS) |
| DNS  | Resolución interna  | Zonas mira.local y reenvío a DNS público ISP |
| DHCP  | Direcciones de VLAN 1 y 2 | Ámbitos 10.10.10.0/24 y 10.10.20.0/24 con IP Helper en los switches; excluye servidores estáticos |
| GPO  | Políticas  | Líneas base de seguridad: contraseñas, bloqueo de cuenta, actualizaciones |
| Grupo de trabajo / integración | Integración de servidores no Windows  | SRV-POSIX y SRV-WEB-MIRA se integran por IP (membresía opcional al dominio) |

20 / 33  
**4.18.3 Estructura de UO y grupos** 

mira.local 

├── ADMIN (personal de TI → permisos completos de red/servidores) ├── OPERADORES (personal de la IM → acceso al panel MIRA)   
└── CIUDADANOS (cuentas externas limitadas al frente web) 

| Grupo  | Miembros  | Permisos |
| :---- | :---- | ----- |
| G-ADMINS-IT  | Thomas, Fabio, Riccardo  | Acceso a SRV-AD-DNS, SRV-POSIX, SRV WEB-MIRA |
| G-OPERADORES  MIRA | usuarios demo de operadores | Acceso al panel MIRA y a la carpeta de reportes |
| G-VISITANTES  | cuentas invitadas  | Acceso limitado a recursos compartidos públicos |

**4.18.4 Integración con MIRA** 

Autenticación del panel interno: los operadores inician sesión contra el dominio (opción LDAP/AD) o contra la base de usuarios propia sincronizada con AD. 

Recursos compartidos Samba (SMB)/NFS para intercambio de archivos entre servidores si se requiere. Los servicios de red (DNS/DHCP) del dominio son los que publican las resoluciones de los servidores del proyecto (SRV-AD-DNS, SRV-POSIX, SRV-WEB-MIRA). 

**4.18.5 Seguridad** 

Políticas de contraseñas robustas (GPO) y bloqueo de cuenta tras intentos fallidos. Segmentación: estos servicios escuchan solo en la VLAN 3 (Servidores) y su administración se restringe a VLAN 1\. 

Revisiones de configuración con el laboratorio académico (Hito 3). 

**4.19 Conectividad y seguridad de redes** 

**4.19.1 Objetivo** 

Diseñar e implementar, siguiendo las guías de configuración del fabricante (Cisco, 2026), una red segmentada y segura para el entorno del proyecto MIRA: separación de tráfico por VLAN (administración, usuarios y servidores), zona **DMZ** para los servicios publicados desde Internet y control perimetral mediante **firewall Cisco ASA 5505** con **VPN IPsec** para la administración remota. 

**4.19.2 Topología de red** 

El esquema de la topología se presenta en el **Anexo E2 (ver Figura 1)**. La tabla siguiente detalla los dispositivos representados en ella: 

21 / 33

| Dispositivo  | Ubicación  | IP |
| ----- | ----- | :---- |
| Firewall perimetral  | Perimetral  | WAN 200.10.10.2 / INSIDE 10.10.x.1 / DMZ 10.10.40.1 |
| Router de borde  | Perimetral  | VPN IPsec hacia administradores |
| Switch core (capa 3)  | Núcleo  | enrutamiento inter-VLAN (SVI), trunk 802.1Q (VLAN 1-3) |
| Switches de acceso (capa 2)  | Por VLAN  | acceso VLAN 1 y 2 |
| SRV-AD-DNS (Windows)  | VLAN 3  | 10.10.30.13 |
| SRV-POSIX (Ubuntu)  | VLAN 3  | 10.10.30.14 |
| SRV-WEB-MIRA  | DMZ  | 10.10.40.10 |

Nota: la topología se describe por función de cada dispositivo (firewall perimetral, switch core de capa 3, switches de acceso de capa 2). El equipamiento concreto utilizado para la simulación y sus restricciones de licenciamiento se detallan en la sección 4.12.3.  
**4.19.3 Plan de direccionamiento (VLAN)** 

| ID  | VLAN  | Red/CIDR  | Gateway  | Asignación  | Uso |
| ----- | ----- | ----- | ----- | ----- | :---- |
| 1  | Administradores  | 10.10.10.0/24  | 10.10.10.1  | DHCP  (.10–.254) | Personal TI, gestión y monitoreo |
| 2  | Usuarios  | 10.10.20.0/24  | 10.10.20.1  | DHCP  (.10–.254) | Operadores municipales |
| 3  | Servidores  | 10.10.30.0/24  | 10.10.30.1  | Estática (.11+)  | App, DB, AD/DNS, POSIX |
| N/A  | DMZ  | 10.10.40.0/24  | 10.10.40.1  | Estática  | SRV-WEB-MIRA .10, bastión .20 |
| N/A  | WAN  | 200.10.10.0/30  | 200.10.10.1  | Estática  | Enlace ISP |

**4.19.4 Segmentación y políticas de tráfico (ACL)** 

El firewall perimetral implementa las siguientes reglas: 

| Origen  | Destino  | Servicio  | Acción |
| :---- | :---- | :---- | ----- |
| Internet  | DMZ (SRV-WEB-MIRA .10) | 80/443  (HTTP/HTTPS) | Permitir (publicación web) |
| INTERNET  | LAN  | —  | Denegar (por defecto) |
| LAN (VLAN 1-3)  | DMZ  | TCP (web), ICMP  | Permitir (salida de servicios) |
| LAN → INTERNET  | —  | NAT sobre carga  | Permitir (salida) |
| VLAN 2 Usuarios | SRV-AD-DNS (10.10.30.13) | UDP/TCP 53, TCP 389, UDP 67 | Permitir (DNS, LDAP, DHCP) |
| VLAN 2 Usuarios | SRV-POSIX (10.10.30.14) | TCP 443 | Permitir (panel interno) |
| VLAN 2 Usuarios  | VLAN 3 Servidores  | Resto de puertos | Denegar (segmentación) |
| VLAN 1 (admin)  | VLAN 3 Servidores  | 22, 53, 88, 389, 445, 3389, 5432, 5985 | Permitir (administración) |
| Administración  remota | FW  | VPN IPsec  | Permitir (túnel) |
| VLAN 2 y VLAN 3 | VLAN 1 (admin) | TCP 22, 3389 (SSH/RDP) | Denegar (solo TI administra desde VLAN 1\) |
| DMZ (SRV-WEB-MIRA 10.10.40.10) | SRV-POSIX (10.10.30.14) | TCP 443 | Permitir (proxy Nginx hacia la app) |
| DMZ | Resto de INSIDE (VLAN 1-3) | — | Denegar |

Principios: **deny by default**, zona por zona; el servicio web se publica **solo** desde la DMZ; el tráfico hacia la base de datos se mantiene en VLAN 3 y la segmentación impide el acceso lateral desde Usuarios. 

**4.19.5 VPN IPsec** 

Túnel site-to-site entre la sede del laboratorio (equipos de administración) y la red del proyecto (VLAN 1). 

Parámetros: IKEv2/IPsec, AES-256, DH-14, PSK; implementación y validación en el laboratorio (Hito 3). 

22 / 33  
**4.19.6 NAT** 

**PAT/DNAT**: publicación del SRV-WEB-MIRA (DMZ) hacia Internet (puertos 80/443). NAT sobre carga para la salida de LAN a Internet.   
Característica: la base de datos y los servicios internos no son accesibles desde Internet (no publicados). 

**4.19.7 Monitoreo y gestión** 

Monitoreo de puertos y estado de enlaces del core/access (SNMP) y del firewall. Logs de seguridad del ASA centralizados; revisión periódica de eventos. Herramientas de diagnóstico: ping, traceroute, wireshark para verificación de políticas (evidencia en el Anexo E). 

**4.19.8 Redundancia y disponibilidad** 

RSTP en los switches de acceso para evitar bucles y permitir redundancia de enlaces. El switch core concentra el enrutamiento inter-VLAN (SVI) con los recursos de la VLAN 3 y la DMZ en alta disponibilidad por monitoreo de enlaces. 

**4.19.9 Seguridad de red reforzada** 

Administración remota exclusivamente desde la VLAN de administración (VLAN 1\) mediante SSH/RDP. Actualización de firmware y parches en switches/firewall/equipos de red. Política de contraseñas y bloqueo de puertos no utilizados en los switches de acceso (DHCP snooping opcional). 

**4.20 Pruebas y validación** 

**4.20.1 Estrategia de pruebas** 

Se combinan pruebas **funcionales** (caja negra sobre los casos de uso), **de integración** (Laravel/Pest para la API y la base de datos) y **de infraestructura** (verificación de conectividad y políticas de red). El conjunto de casos de prueba se documenta en la **Tabla 7.4**. 

**4.20.2 Niveles de prueba** 

| Nivel  | Alcance  | Momento |
| :---- | :---- | :---- |
| Unitarias  | Servicios y modelos (validación de datos, reglas de negocio)  | Durante el desarrollo |
| Integración  | API \+ BD (PostGIS), autenticación y roles  | S7-S8 |
| Funcionales  | Casos de uso: reporte, gestión, mapa  | S8-S9 |
| Infraestructura  | Conectividad VLAN/DMZ, firewall y DNS/DHCP  | S10-S11 |
| Aceptación (UAT)  | Flujo completo del ciudadano y del operador  | S11 |

**4.20.3 Pruebas clave en el Hito 1 (planificadas)** 

**CP-01** Registro de incidencia con ubicación en mapa → se crea con código único y estado *Pendiente*. 

**CP-02** Incidencia sin ubicación → se rechaza con mensaje claro. 

**CP-04** Cambio de estado por operador → historial registrado y notificación al ciudadano. **CP-05** Seguimiento del reporte por un ciudadano registrado (y visitante sin cuenta: solo ve el mapa de pendientes) → muestra estado correcto. **CP-06** Mapa: visualización de incidencias del distrito filtrado. 

23 / 33  
**CP-07** Autenticación de operador y control de permisos (operador no accede a área admin). 

**CP-08** Conectividad: VLAN 2 no alcanza VLAN 3 (segmentación). 

**CP-09** SRV-WEB-MIRA publicado desde Internet solo en 80/443 a través de la DMZ. 

**4.20.4 Métricas** 

Porcentaje de casos aprobados ≥ **90 %** antes del Hito 3 (objetivo OE7). 

Tiempo de respuesta del panel ≤ 3 s con 200 usuarios (RNF01). 

**4.20.5 Gestión de defectos** 

Cada defecto se registra con el código de trazabilidad (T-XXX-TEST-VXX), la evidencia (captura/reporte) y el responsable de corrección. 

Reapertura de casos tras corrección y regresión en los flujos afectados. **4.21 Infraestructura, despliegue y soporte** 

**4.21.1 Arquitectura de despliegue** 

El despliegue objetivo del proyecto se compone de: (1) **SRV-POSIX (10.10.30.14)** con Docker (app Laravel \+ Nginx \+ PostgreSQL/PostGIS), (2) **SRV-AD-DNS (10.10.30.13)** con el entorno Microsoft para identidad y servicios de red, y (3) **SRV-WEB-MIRA (10.10.40.10)** en la DMZ que publica el frente web hacia Internet. 

**4.21.2 Diagrama de despliegue (resumen)** 

El diagrama de implementación se presenta en el **Anexo E3 (ver Figura 2)**. La tabla siguiente resume los componentes representados en él: 

| Componente  | Equipo  | VLAN  | Puertos |
| :---- | :---- | :---- | :---- |
| Web público (Nginx reverse proxy con TLS)  | SRV-WEB-MIRA  | DMZ .10  | 80/443 (externa) |
| API \+ panel  | SRV-POSIX  | VLAN 3 .14  | 443 interno |
| PostgreSQL/PostGIS  | SRV-POSIX  | VLAN 3  | 5432 (solo red interna) |
| AD/DNS/DHCP  | SRV-AD-DNS  | VLAN 3 .13  | 53, 389, 445 (VLAN 1/3) |
| Operadores  | PC VLAN 2  | VLAN 2  | 443 → SRV-POSIX (panel interno; no se publica a Internet, RNF04) |

**4.21.3 Procedimiento de despliegue** 

1\. git clone del repositorio en SRV-POSIX y docker compose up \-d \--build. 2\. Migraciones y *seeders*: docker compose exec app php artisan migrate \--seed. 3\. Publicación del frente web: Nginx en SRV-WEB-MIRA proxy hacia SRV-POSIX (VLAN 3). 

4\. Registro DNS en mira.local (registros A de SRV-AD-DNS, SRV-WEB-MIRA y SRV-POSIX). 5\. Verificación de conectividad y de políticas de firewall. 

6\. Respaldo inicial de la base de datos. 

**4.21.4 Plan de soporte y mantenimiento** 

**Monitoreo**: estado de contenedores, uso de disco y logs (vía cron \+ script de reporte). **Respaldo diario** de PostgreSQL (pg\_dump) con retención de 7 días.   
**Actualizaciones**: parches de seguridad de Ubuntu 24.04 (unattended-upgrades) y revisión de dependencias Laravel/Composer mensual. 

**Incidentes**: procedimiento de registro con etiqueta de trazabilidad T-XXX y comunicación al equipo GPD para la actualización documental. 

24 / 33  
**4.21.5 Requisitos de hardware (laboratorio)** 

2–3 VMs con 4 vCPU/8 GB RAM (SRV-POSIX, SRV-AD-DNS, cliente de prueba). Cisco Packet Tracer para simulación de la topología de red. 

**4.22 Solución integrada** 

**4.22.1 Visión integradora** 

MIRA integra de forma coherente **software, datos, infraestructura y seguridad** bajo un mismo modelo de información y un mismo esquema de red: 

| Dimensión  | Contribución a la solución |
| :---- | :---- |
| Software (DFB)  | Sistema web Laravel 13/PHP 8.4: reporte ciudadano, clasificación por categorías y panel interno |
| Base de datos  | PostgreSQL/PostGIS: georreferenciación, análisis territorial y respaldos |
| POSIX  | SRV-POSIX con Docker: aplicación, Nginx y base de datos |
| Redes MS  | SRV-AD-DNS: dominio mira.local, DNS, DHCP, GPO y agrupamiento por roles |
| Conectividad  | VLAN 1/2/3 \+ DMZ, firewall perimetral, VPN IPsec y segmentación de tráfico |
| Calidad y documentos | Trazabilidad de tareas T-XXX, casos de prueba y documento integrador |

**4.22.2 Flujo integral del sistema (escenario)** 

1\. El ciudadano accede al sitio **http/ https://srv-web-mira** (DMZ) desde su móvil e inicia sesión con su cuenta (sin cuenta solo puede ver el mapa de incidencias pendientes). 2\. Completa el formulario y marca la ubicación en el mapa. 

3\. Selecciona la categoría de la incidencia, que determina el área responsable asociada. 4\. El reporte se persiste en PostgreSQL con geometría PostGIS (índice GIST). 5\. El operador (VLAN 2\) abre el panel, ve el mapa y gestiona el estado (Pendiente → En proceso → Resuelto). 

6\. El ciudadano consulta el estado en la sección de seguimiento de su cuenta y recibe la respuesta. 

7\. La Intendencia explota reportes y mapas de puntos críticos para planificar la recolección. 

**4.22.3 Coherencia de la solución** 

Todas las dimensiones comparten el **mismo plan de direccionamiento** (tabla 4.19.3), los **mismos roles de usuario** (ciudadano/operador/admin) y **los mismos estados de incidencia** (Pendiente → En proceso → Resuelto), lo que garantiza que la documentación de red, los servidores y el software describan un único sistema consistente. 

**4.22.4 Verificación cruzada** 

Cada entregable de las dimensiones se verifica contra la rúbrica (matriz de sección 4.27) y contra los requisitos RF/RNF (Tabla 7.3), asegurando que no existan contradicciones entre el diseño de red, la base de datos y el comportamiento descripto del software. 

**4.23 Conclusiones** 

**4.23.1 Conclusiones del Hito 1** 

En el Hito 1 el equipo Bit Oriental logró: 

1\. **Consolidar la planificación** del proyecto con metodología Scrum, roles y áreas funcionales definidas, y un cronograma alineado a los tres hitos académicos. 

25 / 33  
2\. **Definir el problema** de forma precisa (canales no especializados, falta de georreferenciación y de trazabilidad) y proponer una **solución integral** técnica y económicamente viable. 

3\. **Avanzar el relevamiento** de requisitos y estructurar los RF/RNF preliminares que orientarán el desarrollo del Hito 2\. 

4\. **Diseñar la infraestructura de red** (VLAN 1/2/3 \+ DMZ, ASA 5505, VPN IPsec) como la dimensión más madura de esta etapa, con un plan de direccionamiento aprobado. 5\. **Establecer la metodología de registro documental** (trazabilidad T-XXX) que permite auditar el avance de cada tarea. 

**4.23.2 Lecciones y desafíos para los próximos hitos** 

La **curva de aprendizaje de PostGIS** exige prototipos tempranos en el Hito 2\. 

La **disponibilidad del laboratorio** condiciona la verificación de red y de servidores; se mitiga con simulación (Packet Tracer) y VMs. 

**4.23.3 Perspectiva** 

El proyecto parte de una base sólida para el Hito 2: arquitectura definida, base de datos espacial modelada, requisitos estructurados y una visión integradora que conecta cada dimensión de la rúbrica con una entrega concreta. El enfoque incremental y la disciplina documental son la garantía de cumplir los objetivos OE1–OE9 al cierre del proyecto en noviembre de 2026\. 

**4.24 Bibliografía**

Cisco. (2026). *Cisco ASA 5500 Series Configuration Guide*. https://www.cisco.com/c/en/us/support/security/asa-5500-series-next-generation-firewalls/products-installation-and-configuration-guides-list.html 

Intendencia de Montevideo. (s.f.). *Limpieza urbana* \[Página institucional\]. Recuperado el 10 de septiembre de 2026, de https://www.montevideo.gub.uy/area tematica/ambiente/limpieza-urbana 

Laravel. (2026). *Laravel 13 documentation: Installation, routing, Eloquent, migrations*. https://laravel.com/docs/13.x 

Leaflet. (2026). *Leaflet documentation*. [https://leafletjs.com/reference.html](https://leafletjs.com/reference.html)

Ley N.º 18.331. Protección de Datos Personales y Acción de "Habeas Data". (2008, 11 de agosto). IMPO. https://www.impo.com.uy/bases/leyes/18331-2008 

Microsoft. (2025). *Windows Server 2022 documentation: Active Directory Domain Services*. https://learn.microsoft.com/en-us/windows-server/identity/ad-ds/windows-server-2022 

OSGeo (PostGIS). (2026). *PostGIS 3.4 spatial and geographic objects for PostgreSQL*. https://postgis.net/documentation/ 

PostgreSQL Global Development Group. (2026). *PostgreSQL 16 documentation*. https://www.postgresql.org/docs/16/ 

Schabenberger, O. y Pierce, J. (2026). *Cálculo de riesgos en proyectos de software* (material de la Tutoria de Gestión de Proyectos, ITI). 

Schwaber, K. y Sutherland, J. (2020). *La Guía de Scrum*. [https://scrumguides.org](https://scrumguides.org)

Viar, M. (2026). *Gestión de registro documental: ficha de historial de tareas y trazabilidad* \[Material de tutoría\]. Instituto Tecnológico de Informática. 

*Nota: las fuentes institucionales (IM) se citan según información pública; los datos de la contraparte académica que requieran confirmación se marcarán como supuestos a validar en el cuerpo del documento.* 

26 / 33  
**4.25 Anexos** 

**Anexo A — Metodología de trazabilidad de tareas (guía docente)** 

Resumen de la ficha "Historial de Tarea" de la tutoría de Gestión de Registro Documental (Viar, 2026): ID de tarea (T-XXX), responsable, área (Análisis/Diseño/Desarrollo/BD/Testing/Documentación), estado, versión, fechas, prioridad, herramienta (Miro/GitHub/Trello), descripción/motivo del cambio, resultado, evidencia y revisor. Formato canónico: 

T-001-BD-V0.1 → Tarea 1 · área BD · versión 0.1 

Ciclo recomendado: BACKLOG → ANÁLISIS → DESARROLLO → REVISIÓN → TESTING → APROBADO → PRODUCCIÓN. 

**Anexo B — Diagramas de análisis** 

Diagrama de casos de uso (Ciudadano / Inspector / Administrador) — tablero Miro. Diagrama de clases y de secuencia del flujo de reporte — Miro / UML. 

**Anexo C — Diccionario de datos (Base de datos)** 

Diccionario completo de las tablas: usuario, categoria, area, incidencia, historial\_incidencia (tipos, restricciones, índices, índices espaciales GIST). *(Se amplía en el Hito 2.)* 

**Anexo D — Wireframes del sistema** 

Enlaces al tablero Miro (formulario de reporte móvil, panel del operador, detalle de incidencia, estadísticas). 

**Anexo E — Evidencias de red y verificación de políticas** 

**Anexo E1 — Plan de direccionamiento** 

Tabla de direccionamiento por VLAN (ID, red/CIDR, gateway, tipo de asignación y uso): ver sección 4.19.3. Reglas de control de tráfico aplicadas en el firewall perimetral: ver sección 4.19.4. 

**Anexo E2 — Esquema de topología de red** 

| ▼ INSERTAR DIAGRAMA AQUÍ ▼ |
| :---: |
| **FIGURA 1 — Topología de red del proyecto MIRA** |
| *Reemplazar este cuadro por la imagen del diagrama de topología.* |
| Origen: `Diagrama_Implementacion_Hito1.md` → diagrama 1 (exportar PNG/SVG desde mermaid.live) |
| Debe mostrar: router de borde y enlace ISP 200.10.10.0/30 · firewall perimetral · switch core de capa 3 y switches de acceso de capa 2 · VLAN 1 (10.10.10.0/24), VLAN 2 (10.10.20.0/24) y VLAN 3 (10.10.30.0/24) · DMZ 10.10.40.0/24 con SRV-WEB-MIRA (.10) y bastión (.20) · SRV-POSIX (10.10.30.14) y SRV-AD-DNS (10.10.30.13) · túnel VPN IPsec hacia la VLAN 1 |

**Figura 1.** Topología de red segmentada por VLAN con DMZ y control perimetral (elaboración propia). Referenciada desde la sección 4.19.2. 

**Anexo E3 — Diagrama de implementación (despliegue)** 

| ▼ INSERTAR DIAGRAMA AQUÍ ▼ |
| :---: |
| **FIGURA 2 — Diagrama de implementación (despliegue) del sistema MIRA** |
| *Reemplazar este cuadro por la imagen del diagrama de implementación.* |
| Origen: `Diagrama_Implementacion_Hito1.md` → diagrama 2 (exportar PNG/SVG desde mermaid.live) |
| Debe mostrar: nodo SRV-POSIX con Docker Engine y los contenedores app (Laravel 13 / PHP 8.4), nginx, db (PostgreSQL 16 + PostGIS) y queue · volumen de almacenamiento de imágenes · respaldo cron + pg\_dump · reglas ufw de entrada · origen del tráfico desde SRV-WEB-MIRA (DMZ) |

**Figura 2.** Artefactos desplegados sobre el servidor POSIX y flujo de entrada del tráfico (elaboración propia). Referenciada desde la sección 4.21.2. 

**Anexo E4 — Verificación de políticas** 

Resultados de ping y traceroute entre VLAN, y capturas de Wireshark que validan las reglas de la sección 4.19.4. *(Se completa en el Hito 3, junto con la implementación en laboratorio.)* 

**Anexo F — Actas y registro documental** 

| Acta  | Fecha  | Tema principal  | Ubicación |
| ----- | ----- | ----- | :---- |
| Semana  1 | 02/09/2026  | Kick-off, roles Scrum, rúbrica y plantilla ITI  | Drive PROYECTO MIRA / actas de reunion |
| Semana  2 | 09/09/2026  | Relevamiento de canales, alcance, requisitos RF/RNF | ídem |
| Semana  3 | 16/09/2026  | Cierre del Hito 1: revisión integral y topología de red | ídem |
| Semana  4 | 23/09/2026  | (Planificada) Hito 2: MER, PostgreSQL/PostGIS y migraciones | ídem |
| Semana  5 | 30/09/2026  | (Planificada) Autenticación y formulario de reporte ciudadano | ídem |
| Semana  6 | 07/10/2026  | (Planificada) Mapa interactivo, evidencia fotográfica | ídem |

Cada acta registra orden del día, desarrollo, acuerdos, pendientes (tabla Pendiente/Responsable/Fecha) y próxima reunión. 

Control de versiones del documento integrador: historial en portada y sección 4.26. 27 / 33  
**Anexo G — Glosario** 

| Término  | Definición |
| :---- | :---- |
| Incidencia  | Reporte de un problema de limpieza urbana |
| DMZ  | Zona desmilitarizada: segmento para servicios publicados |
| VLAN  | Red de área local virtual dentro de un switch |
| PostGIS  | Extensión geoespacial de PostgreSQL |
| UAT  | Pruebas de aceptación del usuario |
| RBAC  | Control de acceso basado en roles |

**Anexo H — Especificación de Requisitos de Software (ESRE) según IEEE 830** 

*Versión preliminar del **Hito 1**. Este anexo reorganiza, bajo la estructura de la norma IEEE 830-1998, los requisitos ya presentados en las secciones 4.5, 4.13, 4.14 y en la Tabla 7.3 del cuerpo del documento. En esta etapa los requisitos tienen carácter **preliminar**: se consolidan con el prototipo funcional del Hito 2 y se validan contra los casos de prueba en el Hito 3. Ante cualquier divergencia, prevalece el cuerpo del documento.* 

**H.1 Introducción** 

**H.1.1 Propósito.** Especificar los requisitos funcionales y no funcionales del sistema MIRA (Mapa de Incidencias, Reportes y Atención) para el registro y la gestión de incidencias de limpieza urbana de Montevideo. Está dirigido al equipo Bit Oriental y a los docentes tutores de la Tecnicatura en Redes y Software del ITI, y sirve de base para el diseño, la implementación y las pruebas de los Hitos 2 y 3. 

**H.1.2 Alcance del producto.** MIRA es un sistema web que permite al ciudadano registrado reportar incidencias de limpieza con descripción, categoría, ubicación georreferenciada y evidencia fotográfica, y al personal de la Intendencia de Montevideo gestionarlas mediante un panel interno con listado, mapa y cuaderno de trabajo. Quedan fuera del alcance los puntos enumerados en la sección 4.5.2, entre ellos la integración con sistemas internos de la Intendencia, las aplicaciones móviles nativas, el despliegue en producción física y la integración con modelos de lenguaje (LLM) o servicios externos de inteligencia artificial. 

**H.1.3 Definiciones, acrónimos y abreviaturas.** Se aplican las definiciones del Anexo G — Glosario. Adicionalmente: 

| Término  | Definición |
| :---- | :---- |
| ESRE  | Especificación de Requisitos de Software (IEEE 830-1998) |
| RF / RNF  | Requisito funcional / Requisito no funcional |
| CU  | Caso de uso |
| CP  | Caso de prueba (Tabla 7.4) |
| IM  | Intendencia de Montevideo |
| MIRA  | Mapa de Incidencias, Reportes y Atención (el sistema especificado) |
| GIST  | Índice generalizado de árbol de búsqueda, usado por PostGIS para datos espaciales |
| Incidencia pendiente  | Incidencia registrada que aún no fue tomada por un operador (estado inicial) |

**H.1.4 Referencias.** Las fuentes citadas se detallan en la sección 4.24 (Bibliografía, normas APA). Documentos internos de referencia: plantilla ITI V2.0, rúbrica de evaluación 2026 y Anexos A a G del presente documento. 

**H.1.5 Visión general.** La sección H.2 describe el producto en términos generales (perspectiva, funciones, usuarios, restricciones y suposiciones). La sección H.3 detalla los requisitos específicos: interfaces externas, funciones, rendimiento, restricciones de diseño y atributos del sistema. La sección H.4 presenta la matriz de trazabilidad entre requisitos, casos de uso y casos de prueba. 

**H.2 Descripción general** 

**H.2.1 Perspectiva del producto.** MIRA es un producto nuevo y autónomo, no un componente de un sistema mayor ni un reemplazo de un sistema existente de la IM. Se estructura en tres capas (sección 4.14.4): presentación (vistas Blade y JavaScript con mapa Leaflet sobre tiles de OpenStreetMap), aplicación (Laravel 13 sobre PHP 8.4: controladores y servicios) y datos (Eloquent con SQL y PostGIS). Se despliega sobre contenedores Docker orquestados con Docker Compose, en la infraestructura descrita en las secciones 4.17 a 4.21. 

**H.2.2 Funciones del producto.** En términos generales, el sistema debe permitir: 

a) el registro de incidencias georreferenciadas por parte de ciudadanos autenticados; 
b) la consulta pública, sin autenticación, del mapa de incidencias pendientes sin datos personales; 
c) el seguimiento del estado de los reportes propios por parte del ciudadano; 
d) la clasificación de la incidencia por categoría, con asignación del área responsable asociada; 
e) la gestión interna de incidencias por parte de operadores (listado, filtros, mapa, cambio de estado e historial); 
f) la administración de usuarios, roles, categorías y áreas; 
g) la explotación estadística y la exportación de datos. 

**H.2.3 Características de los usuarios.** Cuatro perfiles, según la sección 4.13.3: 

| Perfil  | Formación técnica esperada  | Frecuencia de uso  | Funciones accesibles |
| :---- | :---- | :---- | :---- |
| Visitante (no registrado) | Ninguna; usuario general de internet | Ocasional  | Mapa público de incidencias pendientes, sin datos personales |
| Ciudadano registrado  | Ninguna; usuario de smartphone | Ocasional (ante una incidencia) | Crear reportes, adjuntar foto, seguimiento e historial propio |
| Inspector / operador IM  | Usuario administrativo, capacitación interna | Diaria (jornada laboral) | Listado y mapa, asignación, cambio de estado, respuesta al ciudadano |
| Administrador  | Perfil técnico / TI  | Semanal  | Usuarios, roles, categorías, áreas, estadísticas y registros de auditoría |

**H.2.4 Restricciones.** 

**R-01 Tecnológicas**: el stack está fijado por la sección 4.12.2 (PHP 8.4 / Laravel 13, Nginx, PostgreSQL 16 con PostGIS, Docker). 
**R-02 De infraestructura**: el sistema opera en la topología segmentada de la sección 4.19 (VLAN 1-3 y DMZ); el panel interno no se publica a Internet. 
**R-03 Legales**: tratamiento de datos personales conforme a la Ley 18.331 de la República Oriental del Uruguay. 
**R-04 Académicas**: el despliegue y la verificación se realizan en laboratorio o simulación (Packet Tracer, VirtualBox), no en producción de la IM. 
**R-05 De estándares**: código conforme a PSR-12 y documentación conforme a la plantilla ITI V2.0. 

**H.2.5 Suposiciones y dependencias.** 

**S-01** No se dispone de datos oficiales de la IM; se utilizan datos sintéticos representativos y supuestos documentados que la contraparte académica debe validar (sección 4.5.3). 
**S-02** El proyecto no tiene vínculo formal con la Intendencia de Montevideo: es un caso de estudio académico. 
**S-03** Se asume disponibilidad de los tiles de OpenStreetMap para el componente de mapa. 
**S-04** Se asume licencia Security Plus en el firewall emulado para habilitar OUTSIDE, INSIDE y DMZ sin restricciones (ver sección 4.12.3). 
**S-05** La disponibilidad del laboratorio académico condiciona la verificación de los requisitos de red e infraestructura. 

**H.3 Requisitos específicos** 

**H.3.1 Interfaces externas** 

**H.3.1.1 Interfaces de usuario.** Interfaz web responsive: formulario público de reporte con mapa interactivo, pantalla de confirmación con código de seguimiento, sección de seguimiento de reportes propios y panel interno con listado, filtros, vista mapa, detalle con historial y portal de estadísticas. Los wireframes de baja fidelidad se mantienen en el tablero Miro (Anexo D). 

**H.3.1.2 Interfaces de hardware.** Cliente: dispositivo con navegador moderno; se utiliza el GPS del dispositivo para la geolocalización opcional. Servidor: 2 a 3 máquinas virtuales de 4 vCPU y 8 GB de RAM (sección 4.21.5). 

**H.3.1.3 Interfaces de software.** Endpoints internos de la sección 4.15.4 (`/api/incidencias`, `/api/incidencias.geojson`, `/api/incidencias/{id}`, `/api/incidencias/{id}/categoria`, `/api/incidencias/{id}/estado`, `/api/estadisticas`); librería Leaflet sobre tiles de OpenStreetMap; PostgreSQL 16 con PostGIS; servicios de dominio del entorno Microsoft (DNS y, opcionalmente, LDAP/AD para la autenticación del panel interno, sección 4.18.4). El sistema **no** consume servicios externos de inteligencia artificial (sección 4.5.2). 

**H.3.1.4 Interfaces de comunicación.** HTTP/HTTPS (80/443) para la publicación web desde la DMZ; HTTPS interno entre SRV-WEB-MIRA y SRV-POSIX; TCP 5432 restringido a la red interna del contenedor; SMTP para las notificaciones por correo. Las políticas de tráfico aplicables son las de la sección 4.19.4. 

**H.3.2 Funciones (requisitos funcionales)** 

Los requisitos conservan la numeración de la sección 4.13.4 y de la Tabla 7.3. Los criterios de aceptación son **criterios a verificar** en los Hitos 2 y 3; ningún requisito se encuentra validado en el Hito 1. 

| ID  | Requisito  | Prioridad  | Criterio de aceptación (a verificar)  | Verifica  | Estado H1 |
| ----- | :---- | ----- | :---- | ----- | ----- |
| RF01  | Registrar una incidencia con descripción, categoría, ubicación y foto opcional; solo usuarios registrados y autenticados | Alta  | Un usuario autenticado crea una incidencia con ubicación marcada; el envío sin ubicación se rechaza con mensaje claro; la foto admite hasta 5 MB | CP-01, CP-02  | Especificado |
| RF02  | Asociar a cada incidencia un código único de seguimiento | Alta  | Toda incidencia creada recibe un código único, visible en la sección de seguimiento de la cuenta | CP-01, CP-05  | Especificado |
| RF03  | Geo-referenciar coordenadas y almacenarlas como geometría PostGIS | Alta  | Las coordenadas se persisten como GEOMETRY(Point, 4326\) con índice espacial GIST | CP-01  | Especificado |
| RF04  | Categorización: el ciudadano elige la categoría (con área responsable asociada); el administrador puede cambiar categoría y área | Alta  | Al crear la incidencia se asigna el área de la categoría elegida; el cambio por el administrador queda registrado en el historial | Previsto H2  | Especificado |
| RF05  | Panel de operadores: listado, filtros por estado, categoría, área y período, detalle y edición | Alta  | El operador filtra el listado y la vista mapa, y accede al detalle de una incidencia | CP-06, CP-07  | Especificado |
| RF06  | Gestión de estados: Pendiente → En proceso → Resuelto (estados canónicos) | Alta  | Solo se permiten las transiciones canónicas; cada cambio actualiza el historial | CP-04  | Especificado |
| RF07  | Asignación de responsable y registro del cuaderno de trabajo | Alta  | Cada cambio registra usuario, fecha y comentario en historial\_incidencia | CP-04  | Especificado |
| RF08  | Notificación al ciudadano cuando cambia el estado | Media  | El ciudadano recibe la notificación por correo o la ve en su sección de seguimiento | CP-04  | Especificado |
| RF09  | Visualización geográfica con filtro por categoría y estado; mapa público limitado a incidencias pendientes sin datos personales | Media  | El visitante no autenticado ve únicamente ubicaciones de incidencias pendientes, sin datos personales | CP-06  | Especificado |
| RF10  | Gestión de usuarios y roles (registro, login, roles, permisos) | Alta  | Un operador autenticado no accede a funciones de administración (RBAC) | CP-07  | Especificado |
| RF11  | Reportes estadísticos por categoría, estado, barrio y período | Media  | Las agregaciones coinciden con los datos de prueba cargados | Previsto H2  | Especificado |
| RF12  | Exportación de datos en CSV para análisis | Media  | El archivo exportado contiene las columnas y el subconjunto filtrado | Previsto H2  | Especificado |

*Nota: RF04 no figura en la Tabla 7.3 del cuerpo del documento y se incorpora aquí para completar la serie RF01–RF12 de la sección 4.13.4; corresponde regularizarlo en la Tabla 7.3 en la próxima versión.* 

**H.3.3 Requisitos de rendimiento** 

**RNF01** Tiempo de respuesta de las páginas del panel menor o igual a 3 segundos con hasta 200 usuarios simultáneos. 
**RNF02** Arquitectura de contenedores que permita replicar servicios (escalabilidad horizontal mediante Docker Compose). 

**H.3.4 Restricciones de diseño** 

Stack tecnológico obligatorio de la sección 4.12.2; arquitectura en tres capas de la sección 4.14.4; estructura de proyecto Laravel de la sección 4.15.2; conformidad con PSR-12, migraciones versionadas y *seeders* para datos de prueba (sección 4.15.5); mensajes de error en español. 

**H.3.5 Atributos del sistema** 

| Atributo  | Requisito  | Especificación |
| :---- | ----- | :---- |
| Seguridad (acceso)  | RNF03  | Autenticación por sesión con BCrypt, protección CSRF y control de acceso basado en roles |
| Seguridad (red)  | RNF04  | El panel interno solo es accesible desde las VLAN internas; el servidor web se publica únicamente desde la DMZ en 80/443 |
| Disponibilidad  | RNF05  | Respaldos diarios de la base de datos y levantamiento de servicios con Docker Compose |
| Usabilidad  | RNF06  | Interfaz responsive y mapa legible en smartphone |
| Portabilidad  | RNF07  | Compatibilidad con Chrome, Edge, Firefox y Safari en móvil |
| Integridad  | RNF08  | Integridad referencial, índices espaciales y transaccionalidad |
| Accesibilidad  | RNF09  | Contraste y etiquetas mínimas conforme a WCAG nivel A en formularios |

**H.3.6 Otros requisitos** 

**RNF10 Legal**: cumplimiento de la Ley 18.331 de protección de datos personales; el mapa público no expone datos personales del denunciante. 
**Modelo de datos**: el modelo conceptual (entidades usuario, categoria, area, incidencia e historial\_incidencia) se describe en la sección 4.14.3 y el diccionario de datos en la sección 4.16.3 y el Anexo C. 

**H.4 Matriz de trazabilidad** 

| Requisito  | Caso de uso  | Caso de prueba  | Dimensión de la rúbrica |
| ----- | ----- | ----- | :---- |
| RF01–RF04  | CU-01 Reportar incidencia  | CP-01, CP-02  | III Programación · IV Base de Datos |
| RF05–RF08  | CU-02 Gestionar incidencia  | CP-04  | III Programación |
| RF02, RF09  | CU-03 Seguir mis reportes / Ver mapa público | CP-05, CP-06  | III Programación |
| RF10  | CU-04 Autenticarse / Administrar usuarios | CP-07  | III Programación · VII Seguridad |
| RF11, RF12  | CU-05 Generar reportes / Exportar datos | Previsto H2  | III Programación · IV Base de Datos |
| RNF04  | —  | CP-08, CP-09  | VII Conectividad y Seguridad |

*Los casos de uso CU-03 a CU-05 se enuncian en la sección 4.14.2 y se detallan, junto con el diagrama de casos de uso, en el Anexo B. Esta especificación se amplía y consolida en el Hito 2 con el prototipo funcional.* 

**4.26 Hoja testigo** 

**Proyecto:** MIRA — Mapa de Incidencias, Reportes y Atención (Bit Oriental, TC4) **Institución:** Instituto Tecnológico de Informática (ITI) 

**Versión del documento:** V1.0 · **Fecha de entrega del Hito 1:** 18/09/2026 **Integrantes:** 

| N.º  | Nombre  | Cédula  | Rol  | Firma |
| :---- | :---- | :---- | :---- | :---- |
| 1  | Thomas Galo  | 5.683.784-9  | Coordinador / GPD  | \_\_\_\_\_\_\_\_\_\_\_\_ |
| 2  | Fabio Fernández  | 4.851.936-6  | Subcoordinador / DFB  | \_\_\_\_\_\_\_\_\_\_\_\_ |
| 3  | Riccardo Inojosa  | 6.668.506-5  | IRS  | \_\_\_\_\_\_\_\_\_\_\_\_ |

**Certificación del equipo:** declaramos que el presente documento integrador es el resultado del trabajo del equipo Bit Oriental en el marco de la Tecnicatura en Redes y Software del ITI, grupo TC4, y que su contenido respeta la plantilla ITI V2.0, las normas APA y la rúbrica de evaluación 2026\. 

**Firma del coordinador:** \_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_ 

**Fecha: \_\_\_/\_\_\_/\_\_\_\_\_\_** 

**Anexo documental — Control de versiones** 

| Fecha  | Versión  | Cambio realizado  | Responsable  | Estado |
| ----- | ----- | :---- | :---- | :---- |
| 01/09/2026  | V0.1  | Creación: carátula, resumen ejecutivo y estructura ITI | Thomas Galo  | En  producción |
| 08/09/2026  | V0.5  | Metodología Scrum, roles, herramientas y relevamiento | Fabio  Fernández | En  producción |
| 16/09/2026  | V0.9  | Red, base de datos, POSIX, Redes MS, pruebas y FODA | Riccardo  Inojosa | En revisión |
| 18/09/2026  | V1.0  | Versión final aprobada para entrega  | Thomas Galo  | Aprobada |

28 / 33  
**4.27 Matriz de alineación con la rúbrica 2026** 

| Dimensión  | Pts  | Secciones del documento  | Estado H1 |
| :---- | :---: | :---- | :---- |
| I. Gestión de Registro Documental | 14  | 4.8.3, 4.11.4, Anexo A, Control de versiones | En producción |
| II. Gestión de Proyecto  | 14  | 4.7, 4.8, 4.9, 4.10, Tablas 7.1–7.2  | En producción |
| III. Tutoría de Programación  | 14  | 4.14, 4.15, 4.22, Tabla 7.3 (RF), Tabla 7.4 (CP), Anexo H (ESRE IEEE 830) | En producción (plan) |
| IV. Tutoría de Base de Datos  | 14  | 4.14.3, 4.16, Anexo C  | En producción |
| V. Tutoría de POSIX  | 14  | 4.17, 4.21  | Diseño / Borrador |
| VI. Tutoría de Redes MS  | 14  | 4.18, 4.21  | Diseño / Borrador |
| VII. Conectividad y Seguridad de Redes | 16  | 4.19, Anexo E  | En producción (completa) |

Escala aplicable: Excelente 90–100 · Bueno 80–89 · Aprobado 60–79 · Insuficiente \< 60\. 

La matriz anterior vincula cada dimensión de la rúbrica con las secciones del documento donde se evidencia su cumplimiento. 

**7\. Tablas mínimas de gestión** 

**Tabla 7.1 — Entregables del proyecto** 

| ID  | Entregable  | Descripción  | Responsable  | Estado H1  | Hito |
| ----- | :---- | :---- | ----- | ----- | ----- |
| EN  01 | Documento integrador ITI (PDF) | Documento único versionado según plantilla | GPD  | En producción  | H1–  H3 |
| EN  02 | Anteproyecto / plan de proyecto | Alcance, cronograma, riesgos y metodología | GPD  | En producción  | H1 |
| EN  03 | Control de versiones y trazabilidad | Etiquetas T-XXX-ÁREA-VXX con historial | GPD  | En producción  | H1–  H3 |
| EN  04 | Relevamiento y requisitos RF/RNF | Documento de requisitos preliminares | CTA  | En producción  | H1–  H2 |
| EN  05 | Diagramas UML (casos de uso, clases) | Análisis y diseño  | CTA (con DFB) | En producción  | H1–  H2 |
| EN  06 | Modelo entidad relación y diccionario | MER \+ diccionario de datos  | CTA  | En producción  | H1–  H2 |
| EN  07 | Script SQL de creación (PostGIS) | Migraciones y seeders  | CTA/DFB  | Borrador  | H2 |
| EN  08 | Prototipo funcional de la aplicación | Reporte, panel, mapa  | DFB  | Pendiente  | H2 |
| EN  09 | Configuración de servidores POSIX | Ubuntu 24.04 \+ Docker  | IRS  | Borrador  | H3 |
| EN  10 | Configuración de servidores Windows | AD/DNS/DHCP/GPO  | IRS  | Borrador  | H3 |
| EN  11 | Topología de red simulada | VLAN 1/2/3 \+ DMZ \+ ASA \+ VPN | IRS  | En producción  | H1–  H3 |

29 / 33

| ID  | Entregable  | Descripción  | Responsable  | Estado H1  | Hito |
| ----- | :---- | :---- | ----- | :---- | ----- |
| EN  12 | Casos de prueba y reporte | Tabla 7.4 \+ evidencias  | CTA  | En producción (plan) | H2–  H3 |
| EN  13 | Acta de cierre y defensa | Presentación final  | GPD  | Pendiente  | H3 |

**Tabla 7.2 — Matriz de riesgos** 

| ID  | Riesgo  | Prob.  | Impacto  | Mitigación  | Responsable  | Estado |
| ----- | ----- | ----- | ----- | :---- | ----- | ----- |
| R01  | Atraso por falta de tiempo de integrantes | Media  | Alto  | Sprints semanales, ajuste de alcance | GPD  | Activo |
| R02  | Documentación  descentralizada | Media  | Alto  | Trazabilidad T-XXX, revisión documental | GPD  | Activo |
| R03  | Dificultad con PostGIS  | Media  | Medio  | Capacitación, apoyo tutor BD | CTA  | Activo |
| R05  | Sin datos reales de la IM  | Alta  | Medio  | Datos sintéticos \+ supuestos validados | CTA  | Activo |
| R06  | Fallas en laboratorio de red | Media  | Medio  | Simulación Packet Tracer | IRS  | Activo |
| R08  | Pérdida de integridad de datos | Baja  | Alto  | Transacciones,  respaldos diarios | IRS/CTA  | Activo |
| R09  | Actas/borradores vacíos en Drive | Alta  | Medio  | Completar actas, revisión semanal | GPD  | Activo |

**Tabla 7.3 — Requisitos funcionales y no funcionales (RF/RNF)** 

| ID  | Tipo  | Descripción  | Prioridad  | Fuente |
| ----- | :---- | :---- | ----- | ----- |
| RF01  | Funcional  | Registrar incidencia (descripción, categoría, ubicación, foto); requiere usuario registrado | Alta  | Relevamiento |
| RF02  | Funcional  | Código único de seguimiento (visible en la cuenta del usuario) | Alta  | Relevamiento |
| RF03  | Funcional  | Geolocalización y almacenamiento geométrico en PostGIS | Alta  | Relevamiento |
| RF05  | Funcional  | Panel de operadores con filtros y edición  | Alta  | Relevamiento |
| RF06  | Funcional  | Gestión de estados: Pendiente → En proceso → Resuelto | Alta  | Relevamiento |
| RF07  | Funcional  | Asignación de responsable y historial (cuaderno de trabajo) | Alta  | Relevamiento |
| RF08  | Funcional  | Notificación al ciudadano en cambios de estado  | Media  | Relevamiento |
| RF09  | Funcional  | Mapa con distribución de incidencias y puntos críticos (público: solo incidencias pendientes) | Media  | Anteproyecto |
| RF10  | Funcional  | Gestión de usuarios y roles (RBAC)  | Alta  | Relevamiento |
| RF11  | Funcional  | Reportes estadísticos (por categoría, estado, barrio y período)  | Media  | Anteproyecto |
| RF12 | Funcional | Exportación de datos (CSV) | Media | Anteproyecto |

30 / 33

| ID  | Tipo  | Descripción  | Prioridad  | Fuente |
| ----- | :---- | :---- | ----- | ----- |
| RNF01  | No  funcional | Tiempo de respuesta del panel ≤ 3 s (200 usuarios) | Media  | Relevamiento |
| RNF02  | No  funcional | Arquitectura de contenedores escalable (Docker)  | Media  | Anteproyecto |
| RNF03  | No  funcional | Seguridad de acceso: BCrypt, CSRF, RBAC  | Alta  | Seguridad |
| RNF04  | No  funcional | Publicación web solo por DMZ; panel accesible solo desde VLAN internas | Alta  | Red |
| RNF05  | No  funcional | Respaldos diarios de BD y recuperación probada  | Alta  | Infraestructura |
| RNF06  | No  funcional | Interfaz responsive (móvil)  | Media  | Relevamiento |
| RNF07  | No  funcional | Navegadores modernos (Chrome, Edge, Firefox, Safari) | Media  | Relevamiento |
| RNF08  | No  funcional | Integridad referencial e índices espaciales  | Alta  | Base de datos |
| RNF09  | No  funcional | Accesibilidad mínima (WCAG A)  | Baja  | Calidad |
| RNF10  | No  funcional | Cumplimiento de Ley 18.331 (datos personales)  | Alta  | Legal |

**Tabla 7.4 — Casos de prueba (resumen; 8 casos principales)** 

| ID  | Nombre  | Precondición  | Pasos clave  | Resultado esperado  | Estado |
| ----- | ----- | :---- | :---- | ----- | ----- |
| CP  01 | Registro de incidencia | Mapa cargado ; usuario autenticado | Completar formulario \+ ubicación \+ enviar | Incidencia creada con código y estado Pendiente | Planificado |
| CP  02 | Incidencia sin ubicación | Formulario  | Enviar sin marcar ubicación | Rechazo con mensaje claro | Planificado |
| CP  04 | Cambio de estado | Incidencia  Pendiente | Operador cambia a En proceso | Historial actualizado  | Planificado |
| CP  05 | Seguimiento del reporte | Incidencia creada; usuario autenticado | Abrir el reporte en la sección de seguimiento de su cuenta | Estado y detalle correctos | Planificado |
| CP  06 | Filtro de mapa  | Incidencias  cargadas | Filtrar por distrito/estado | Mapa muestra solo el subconjunto | Planificado |
| CP  07 | Permisos por rol  | Operador  autenticado | Intentar acceso a zona admin | Acceso denegado  | Planificado |
| CP  08 | Segmentación de red | Red simulada  | Probar comunicación VLAN 2 → VLAN 3 | Tráfico denegado (ACL) | Planificado |
| CP  09 | Publicación  DMZ | ASA  configurado | Acceso externo a SRV-WEB-MIRA  80/443 | Acceso permitido; resto bloqueado | Planificado |

*(Los casos CP-10 a CP-30 se detallarán en el Hito 2 con el prototipo funcional: validaciones, carga de imágenes, notificaciones, estadísticas, backups y pruebas de integración y carga.)* 

31 / 33  
**4.28 Evidencias técnicas mínimas** 

Las evidencias del Hito 1 se concentran en la dimensión de **Conectividad y Seguridad de Redes** (madura) y en los **registros documentales**. 

**Evidencia 1 — Plan de direccionamiento por VLAN (canónico)** 

| ID  VLAN | Nombre  | Red/CIDR  | Gateway  | Asignación  | Uso principal |
| :---- | ----- | ----- | ----- | ----- | :---- |
| 1  | Administradores  | 10.10.10.0/24  | 10.10.10.1  | DHCP  (.10–.254) | Personal de TI, gestión y monitoreo |
| 2  | Usuarios  | 10.10.20.0/24  | 10.10.20.1  | DHCP  (.10–.254) | Operadores municipales |
| 3  | Servidores  | 10.10.30.0/24  | 10.10.30.1  | Estática (.11+)  | SRV-AD-DNS .13, SRV POSIX .14 |
| N/A  | DMZ  | 10.10.40.0/24  | 10.10.40.1  | Estática  | SRV-WEB-MIRA .10, bastión .20 |
| N/A  | WAN  | 200.10.10.0/30  | 200.10.10.1  | Estática  | Enlace ISP (FW .2) |

**Evidencia 2 — Topología de red (resumen textual)** 

Perímetro: firewall perimetral (WAN 200.10.10.2 · DMZ 10.10.40.1 · INSIDE 10.10.x.1). Núcleo: switch core de capa 3 que interconecta VLAN 1–3 con trunk 802.1Q; switches de acceso de capa 2; SRV-WEB-MIRA 10.10.40.10 en DMZ; SRV-AD-DNS y SRV-POSIX en VLAN 3\. VPN IPsec para administración remota. *(Diagrama gráfico: Anexo E2.)* 

**Evidencia 3 — Registro documental (muestra del historial de tareas)** 

| Tarea  | Área  | Versión  | Herramienta  | Estado  | Evidencia |
| :---- | :---- | :---- | :---- | :---- | :---- |
| T-001  | BD  | V0.1  | Miro/SUP  | En producción  | Captura del MER |
| T-002  | DOC  | V0.3  | Word  | En producción  | Documento integrador |
| T-003  | RED  | V0.2  | Packet Tracer  | En producción  | Topología y VLAN |
| T-004  | TEST  | V0.1  | Docs  | En producción  | Tabla 7.4 |

**Evidencia 4 — Control de versiones del documento** 

Historial completo en la portada (sección 4.26): V0.1 → V0.5 → V0.9 → V1.0 con autores y fechas. 

**4.29 Checklist de entrega (Hito 1\)** 

| \#  | Verificación  | Estado |
| :---: | :---- | :---- |
| 1  | Carátula ITI completa (institución, integrantes, fecha, versión)  | ✔ |
| 2  | Índice de contenidos (lista numerada de secciones)  | ✔ |
| 3  | Resumen ejecutivo (sección 4.1)  | ✔ |
| 4  | Estructura de 26 secciones (4.1–4.26) completa  | ✔ |
| 5  | Tabla 7.1 Entregables completa  | ✔ |
| 6  | Tabla 7.2 Riesgos completa  | ✔ |
| 7  | Tabla 7.3 Requisitos RF/RNF completas  | ✔ |

32 / 33

| \#  | Verificación  | Estado |
| :---: | :---- | ----- |
| 8  | Tabla 7.4 Casos de prueba definidos  | ✔ |
| 9  | Sin textos guía \[...\] en el documento  | ✔ |
| 10  | Formato: A4 (márgenes 2,5 cm), Arial 11, interlineado 1,15, justificado  | ✔ |
| 11  | Referencias en normas APA (sección 4.24)  | ✔ |
| 12  | Control de versiones y hoja testigo firmada y fechada (verificar antes de exportar)  | ✔ |
| 13  | Esquema de red canónico (VLAN 1/2/3 \+ DMZ \+ WAN) en toda la documentación  | ✔ |
| 14  | Versiones de tecnologías consistentes en todo el documento (Laravel 13 / PHP 8.4)  | ✔ |
| 15  | Exportación final a PDF y subida a Campus Virtual / Drive  | En proceso |

33 / 33

