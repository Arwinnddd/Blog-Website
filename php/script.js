var pass = document.getElementById("password");
var msg = document.getElementById("message");
var str = document.getElementById("strength");

pass.addEventListener('input', () => {
    let passwordValue = pass.value;
    let strength = "";

    if (passwordValue.length > 0) {
        msg.style.display = "block";
    } else {
        msg.style.display = "none";
    }

    // Check password strength using regex
    let weakPattern = /[a-z]/;
    let mediumPattern = /(?=.*[a-z])(?=.*[0-9])/;
    let strongPattern = /(?=.*[a-z])(?=.*[0-9])(?=.*[A-Z])(?=.*[\W])/;

    if (strongPattern.test(passwordValue) && passwordValue.length >= 8) {
        strength = "strong";
        str.className = "#26d730";
        msg.style.color = "#26d730"; 
        pass.style.borderColor = "#26d730";

    } else if (mediumPattern.test(passwordValue) && passwordValue.length >= 6) {
        strength = "medium";
        str.className = "yellow";
        msg.style.color = "yellow"; 
        pass.style.borderColor = "yellow";


    } else {
        strength = "weak";
        str.className = "#ff5925";
        msg.style.color = "#ff5925"; 
        pass.style.borderColor = "#ff5925";
    }

    str.innerHTML = strength;
});

let eyeicon = document.getElementById("eyeicon");
let password = document.getElementById("password");

eyeicon.onclick = function(){
    if(password.type == "password"){
        password.type = "text";
        eyeicon.src = "images/view.png";
    } else {
        password.type = "password";
        eyeicon.src = "images/hide.png";
    }
}

