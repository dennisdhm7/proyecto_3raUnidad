const messages = [
    { id: 1, message: "Hola", answer: 2, suggestions: [1, 2] },
    { id: 2, message: "Hola, soy el bot del Restaurante-Cevichería Capitán Chino. ¿En qué puedo ayudarle?", answer: 0, suggestions: [3, 4, 5] },
    { id: 3, message: "¿Cuál es el horario de apertura?", answer: 6, suggestions: [4, 5] },
    { id: 4, message: "¿Dónde están ubicados?", answer: 7, suggestions: [3, 5, 9] },
    { id: 5, message: "¿Que tipo de pagos aceptan?", answer: 8, suggestions: [4, 3, 9] },
    { id: 6, message: "Abrimos todos los días desde las 08:00AM hasta las 11:30PM; excepto los martes que solo abrimos desde las 08:00AM hasta las 02:00PM", answer: 0, suggestions: [4, 5, 9] },
    { id: 7, message: "Nos encontramos en Calle Santa Lucía #1548, Lima; Perú. Le invitamos a visitarnos cuando desee.", answer: 0, suggestions: [3, 4, 10] },
    { id: 8, message: "Aceptamos pagos en efectivo y tarjetas si va a consumir en el local y pagos solo por tarjeta si desea hacer un pedido a domicilio", answer: 0, suggestions: [9, 10] },
    { id: 9, message: "¿Cómo puedo hacer un pedido?", answer: 11, suggestions: [3, 4, 10] },
    { id: 10, message: "¿Cómo puedo hacer una reservación?", answer: 12, suggestions: [4, 5, 9] },
    {
        id: 11,
        message: "Debes registrarte en nuestra página e ir a la sección de menú y agregar los productos que desee al carrito de compras; luego va al apartado de carrito de compras en el menú lateral de nuestra web y encontrará un formulario que debe rellenar para realizar el pedido",
        answer: 0,
        suggestions: [4, 5],
    },
    {
        id: 12,
        message: 'Ve al apartado de Mis Reservaciones en nuestra web una vez registrado en el sistema y haga click en el botón "Agregar" y rellene el formulario con la información que le pediremos. Debe seleccionar de las mesas disponibles según la cantidad de personas para las que va a reservar',
        answer: 0,
        suggestions: [4, 5, 9],
    },
]

export default messages
