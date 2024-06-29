document.getElementById("campo").addEventListener("keyup", getClientes);

function getClientes() {
    let inputCampo = document.getElementById("campo").value;
    let lista = document.getElementById("lista");

    if (inputCampo.length > 0) {
        let url = "getClientes.php";
        let formData = new FormData();
        formData.append("campo", inputCampo);

        fetch(url, {
            method: "POST",
            body: formData,
            mode: "cors"
        }).then(response => response.json())
            .then(data => {
                lista.style.display = 'block';
                lista.innerHTML = "";
                data.forEach(cliente => {
                    let li = document.createElement('li');
                    li.textContent = `${cliente.nombre} ${cliente.apellido_paterno} ${cliente.apellido_materno}`;
                    li.onclick = () => mostrar(cliente);
                    lista.appendChild(li);
                });
            })
            .catch(err => console.log(err));
    } else {
        lista.style.display = 'none';
    }
}

function mostrar(cliente) {
    document.getElementById("campo").value = `${cliente.nombre} ${cliente.apellido_paterno} ${cliente.apellido_materno}`;
    document.getElementById("clienteID").value = cliente.clienteID;
    lista.style.display = 'none';
}

document.addEventListener("DOMContentLoaded", function () {
    const datePicker = document.getElementById("date-picker");
    const errorMessage = document.getElementById("error-message");

    const today = new Date().toISOString().split('T')[0];
    datePicker.setAttribute("min", today);

    datePicker.addEventListener("change", function () {
        const selectedDate = new Date(datePicker.value);
        const currentDate = new Date();

        if (selectedDate < currentDate) {
            errorMessage.textContent = "La fecha seleccionada no puede ser anterior a hoy. Selecciona una fecha a partir de hoy.";
        } else {
            errorMessage.textContent = "";
        }
    });
});

