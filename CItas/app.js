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
                    li.textContent = `${cliente.nombre} ${cliente.apellido_paterno} ${cliente.apellido_materno} - ${cliente.telefono}`;
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
    getVehiculos(cliente.clienteID);
}

function getVehiculos(clienteID) {
    let url = "getVehiculos.php";
    let formData = new FormData();
    formData.append("clienteID", clienteID);

    fetch(url, {
        method: "POST",
        body: formData,
        mode: "cors"
    }).then(response => response.json())
        .then(data => {
            let listaVehiculos = document.getElementById("lista-vehiculos");
            listaVehiculos.innerHTML = "";
            if (data.length > 0) {
                listaVehiculos.style.display = 'block';
                data.forEach(vehiculo => {
                    let li = document.createElement('li');
                    li.textContent = `${vehiculo.marca} ${vehiculo.modelo} (${vehiculo.año})`;
                    li.onclick = () => seleccionarVehiculo(vehiculo);
                    listaVehiculos.appendChild(li);
                });
            } else {
                listaVehiculos.style.display = 'none';
            }
        })
        .catch(err => console.log(err));
}

function seleccionarVehiculo(vehiculo) {
    document.getElementById("vehiculoSeleccionado").value = `${vehiculo.marca} ${vehiculo.modelo} (${vehiculo.año})`;
    document.getElementById("vehiculoID").value = vehiculo.vehiculoID;
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
