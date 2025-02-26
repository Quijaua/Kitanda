document.addEventListener("DOMContentLoaded", function () {
    const addButton = document.getElementById("addButton");
    const container = document.getElementById("container");
    let divCount = 0;
    
    addButton.addEventListener("click", function () {
        if (divCount < 4) {
            divCount++;
            
            const newDiv = document.createElement("div");
            newDiv.classList.add("added-div");
            newDiv.textContent = "DIV " + divCount;
            
            container.appendChild(newDiv);
        } else {
            alert("Limite de 4 DIVs atingido!");
        }
    });
});