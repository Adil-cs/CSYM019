function myClickFunction() {
  var element = document.getElementById("heading1");
  element.firstChild.nodeValue = "New Heading";
}

function myClickFunctionpara() {
  var element = document.getElementById("paragraph1");
  element.firstChild.nodeValue = "New paragraph text";
}

function myLoadFunction() {
  var element = document.getElementById("heading1");
  element.addEventListener("click", myClickFunction);

  var element = document.getElementById("paragraph1");
  element.addEventListener("click", myClickFunctionpara);
}

function changecolor(){
    var element = document.getElementById("heading1");
    console.log("movement detetcted")
    element.style.color="red"
}

function changecolorback(){
    var element = document.getElementById("heading1");
    console.log("movement detetcted")
    element.style.color="black"
}



document.addEventListener("DOMContentLoaded", myLoadFunction);
document.addEventListener("mouseenter",changecolor)
document.addEventListener("mouseleave",changecolorback)