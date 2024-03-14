<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Display User Input</title>
</head>
<body>
<div id="userData"></div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('data.json')
    .then(response => response.json())
    .then(data => {
        const userDataDiv = document.getElementById('userData');
        data.forEach(item => {
            const img = document.createElement('img');
            img.src = item.image;
            const text = document.createElement('p');
            text.textContent = item.text;
            userDataDiv.appendChild(img);
            userDataDiv.appendChild(text);
        });
    })
    .catch(error => console.error('Error fetching data:', error));
});
</script>
</body>
</html>
