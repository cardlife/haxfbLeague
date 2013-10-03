var xmlhttp;

function sendAjax(url, parameters, divName) {
    xmlhttp = GetXmlHttpObject();
    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    xmlhttp.onreadystatechange = stateChanged;
    xmlhttp.divName = divName;
    xmlhttp.open('POST', url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.setRequestHeader("Content-length", parameters.length);
    xmlhttp.setRequestHeader("Connection", "close");
    xmlhttp.send(parameters);

}

function stateChanged() {
    if (xmlhttp.readyState == 4) {
        //var newdiv = document.createElement("div");
        //newdiv.innerHTML = xmlhttp.responseText;
        var container = document.getElementById(this.divName);
        container.innerHTML = "";
        //container.appendChild(newdiv);
        container.innerHTML = xmlhttp.responseText;
    }
}

function GetXmlHttpObject() {
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        return new XMLHttpRequest();
    }
    if (window.ActiveXObject) {
        // code for IE6, IE5
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
}

function toggleVisibility(elementId, divClassName) {
    if (document.getElementById) {
        var theElement = document.getElementById(elementId);
        if (theElement) {
            hideAllDivs(divClassName);
            if (theElement.style.display == 'block') {
                theElement.style.display = 'none';
            }
            else {
                theElement.style.display = 'block';
            }

        }
    }
}

function hideAllDivs(divClassName) {
    var divArray = document.getElementsByTagName("div");
    for (var i = 0; i < divArray.length; i++) {
        if (divArray[i].className == divClassName) {
            divArray[i].style.display = 'none';
        }
    }
}
