function updateContent(){
    var element=document.getElementById('heading1');
    element.firstChild.nodeValue='New changed Heading';
}

document.addEventListener('click', updateContent)