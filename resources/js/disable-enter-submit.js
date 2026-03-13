document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('input[type="text"], input[type="number"]');

    inputs.forEach((input, index) => {
        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault(); // Evita el envío del formulario
                const nextInput = inputs[index + 1]; // Obtiene el siguiente campo
                if (nextInput) {
                    nextInput.focus(); // Mueve el foco al siguiente campo
                }
            }
        });
    });
});