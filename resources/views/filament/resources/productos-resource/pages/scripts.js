document.addEventListener('DOMContentLoaded', function() {
    // Your script here

    // Example: Function to display an alert when a button is clicked
    const button = document.getElementById('myButton');
    if (button) {
        button.addEventListener('click', function() {
            alert('Button clicked!');
        });
    }
});