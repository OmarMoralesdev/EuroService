document.getElementById("campo").addEventListener("keyup", getClientes);

function getClientes() {
    let inputCampo = document.getElementById("campo").value;
    let lista = document.getElementById("lista");

    if (inputCampo.length > 0) {
        let url = "../Buscador/getClientes.php";
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
                    li.classList.add('list-group-item');
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
    document.getElementById("nombre").value = cliente.nombre;
    document.getElementById("apellido_paterno").value = cliente.apellido_paterno;
    document.getElementById("apellido_materno").value = cliente.apellido_materno;
    document.getElementById("lista").style.display = 'none';
}
