    document.getElementById("search").addEventListener("keyup", getNombres)
    function getNombres(){
        let inputCP = document.getElementId("search").value
        let list = document.getElementId("list")

        let url = "inc/getNombres.php"
        let formData = new FormData()
        formData.append("search", inputCP)

        fetch(url,{
            method: "POST",
            body: formData,
            mode: "cors"
        }).then(response => response.json())
        .then(data => {
            list.style.display = 'block'
            list.innerHTML = data
        })
        .catch(err => console.log(err))
    }