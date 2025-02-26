document.addEventListener("DOMContentLoaded", function () {
  const imagemInputs = document.querySelectorAll(".imagemInput");
  
  imagemInputs.forEach(function (input) {
      const imagemPreviewId = input.getAttribute("id").replace("input", "imagemPreview");
      const imagemPreview = document.getElementById(imagemPreviewId);
      
      input.addEventListener("change", function () {
          const arquivo = input.files[0];
          
          if (arquivo) {
              const leitor = new FileReader();
              
              leitor.onload = function (e) {
                  imagemPreview.src = e.target.result;
              };
              
              leitor.readAsDataURL(arquivo);
          } else {
              imagemPreview.src = "#";
          }
      });
  });
});