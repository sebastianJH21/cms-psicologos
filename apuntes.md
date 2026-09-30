SDD = Spec Driven Development:
Primero definimos exactamente que debe hacer un sistema / app / funcionalidad y luego el agente escribe el código.


Como trabajar con los modelos de IA, cual usar y para que usarlos:
- Planes de implementación: Opus
- Tareas complejas, largas o muchas seguidas: Opus
- Pocas tareas muy simples, esteticas o repetitivas simples: Haiku
- Preguntas del proyecto o preguntas generales: Haiku
- Pocas tareas funcionales, sencillas o de dificultad mediana: Sonnet
- 1 funcionalidad mediana: Sonnet 


Trucos para ahorrar tokens y creditos en agentes de ia:
- Git entre specs, features y entre fases para no recargar el contexto en caso de caos.
- Usar el mismo chat (siempre que se pueda)
- Compactar el chat
- Crear un fichero map.md

    Genera un MAP.md del proyecto.

    Reglas:
        - Explora el repositorio de forma estructurada.
        - No entres en la logica de negocio profunda.
        - Solo identifica estructura, modulos y archivos principales.
        - No modifiques el codigo.
        - No hagas refactors.

    - A partir de ahora usa MAP.md como referencia principal del proyecto, no realices una exploración completa del repositorio salvo que sea estrictamente necesario.

- Usar un fichero de Agentes (para definir todas las especificaciones del proyecto)
- Usar el modelo adecuado para cada tareas.
- No me expliques nada, simplemente avisame cuando hayas terminado (caveman)



Como hacer un fichero de agentes:
# -> nivel de sistema (documento completo)
## -> modulos / bloques grandes de funcionalidades (Autenticacion, Blog, Pacientes, Reservas, etc)
### -> funcionalidades dentro de un modulo
bullets -> requisitos concretos


La estructura de un fichero de agentes (AGENTS.md o CLAUDE.md):
- Estructura dentro del fichero CLAUDE.md


SDD hibrido vs SDD estricto:

SDD hibrido:
Mismas reglas del sdd tradicional, combinar ingenieria de prompts y vibe coding descriptivo, igualmente poniendo reglas y limites en los prompts e indiciones en el fichero de agentes. El resultado es identico si explicas todo bien. Más facil y fluido de hacer.

SDD estricto: 
Lista de funcionalidades más escueta pero más ordenada, a veces quizas con menos contexto de como debe quedar la app, pero si con más seguridad a nivel de qui si y que no puede hacer el agente con el codigo que genere.


La estructura de una SPEC:

## Funcionalidad: Nombre de la featura

### Objetivo
Que problema resuelve

### Entradas
- Campo 1
- Campo 2
- Campo 3

### Salidas
- Resultado esperado

### Reglas
- Restricciones
- Validaciones

### Comportamiento
- Paso 1
- Paso 2
- Paso 3


Prompts para exprimir al agente de IA al maximo:

- Crear planes de implementación:

Crea un plan de implementación detallado para que un agente de ia (claude code con claude sonnet) sea capaz de desarrollar el proyecto y todas las funcionalidades descritas en el fichero @CLAUDE.md Por ahora no crees codigo, solo crea el plan de implementación completo.

- Prompt entre fases:

He terminado la FASE 4 del fichero @CLAUDE.md

Ahora procede a hacer la FASE 5.

Contexto:
- Especificacioon en @CLAUDE.md (funte de verdad funcional)
- Plan de implementacion en @plan-implementacion.md (orden de ejecucion obligatoria)
- Estado actual del sistema en @project-map.md (fuente de verdad de estado real)
- Debes seguir el plan de implementacion estrictamente, sin replantearlo.

Jerarquia de conflicto:
1. @CLAUDE.md (que construir)
2. @plan-implementacion (como construirlo)
3. @project-map.md (estado actual del sistema)

Reglas de ejecucion:
- Consulta primero @project-map.md antes de empezar cualquier tarea.
- Ejecuta las tareas en orden exacto, sin saltarte ninguna.
- No entres en modo de planificacion, analisis global ni rediseño.
- No realices pruebas tu mismo: implementa y avisame cuanto esté listo para validar.
- Solo pregunta si existe un bloqueo tecnico real que impida continuar.

Reglas SDD obligatorias:
- Despues de cada cambio relevante, actualiza @project-map.md con:
- cambios realizados
- nuevos modulos / rutas / entidades
- decisiones tecnicas tomadas
- impacto en el sistema

Control de consistencia:
- No inventes estado en @project-map.md
- Si hay discrepancia entre código y mapa, el codigo tiene prioridad y el map debe correjirse.

Alcance:
- Manten los cambios dentro de la fase actual.
- No modifiques funcionalidaes fuera del scope de la fase que estamos desarrollando, salvo dependencia directa.

Salida esperada:
- Marca cada sub-tarea como completada.
- Al finalizar la fase:
    - resume lo implementado
    - sincroniza completamente @project-map.md con el estado real del sistema
    - marca la fase como completada (en el map, en las tareas y en el plan de implementacion)
    - No me expliques nada, simplemente avisame cuando hayas terminado


Como funciona el flujo de trabajo con el SDD y las fases de desarrollo:
- Planificacion
- Ejecutar prompt entre fases
- Probar el resultado
- Correcciones
- Mandar al agente a corregir o mejorar
- Volver a ejecutar prompt entre fases
  """


Como hacer cambios y modificaciones en un proyecto:
fixes.md



Diferentes opciones para hacer SDD:
- Spec Kit
- OpenSpec


### Promp para crear plan de implementacion y mapa del proyecto
Crea un plan de implementación detallado en @plan-implementacion.md para que un agente de ia (claude code con claude sonnet) sea capaz de desarrollar el proyecto y todas las funcionalidades descritas en el fichero @CLAUDE.md . Por ahora no crees codigo, solo crea el plan de implementación completo.

Tambien crear un fichero project-map.md donde listes las tareas definidas en cada fase según @CLAUDE.md y @plan-implementacion.md, ya que uno define las tareas generales y el otro las detalla y crea sub tareas si es necesario. Por lo tanto, debes representar en tareas ejecutadas el estado actual del proyecto.

El proyecto, ya se desarrollo y no se dejaron los archivos del plan de implementacion y mapa del proyecto, tu tarea es entender que hace y que funcionalidades tiene el proyecto según lo que encuentras en @CLAUDE.md .

Es muy probable que encuentres mas tareas o funcionalidades de las que existen en @CLAUDE.md , agregalas en el plan implementación y tambien en el mapa del proyecto si es necesario agregalas en @CLAUDE.md en al fase que le corresponda, pero no modifiques las anteriores.

En el mapa de proyecto debes poner las tareas como completadas, y cada uno de las fases tambien como finalizadas. Si llegas a encontrar funcionalidades incompletas no las marques como completadas para identificarlas y ver si es necesario terminarlas o eliminarlas.

Orden de los archivos:
1. plan-implementacion.md
2. project-map.md


### Promp para push
- Realiza un push a la rama XXXX con un commit estructurado y buenas practicas.
