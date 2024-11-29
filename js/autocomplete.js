const resultsBox = document.querySelector(".result-box");
const inputBox = document.getElementById("input-box");

inputBox.addEventListener("keyup", function() {
    let input = inputBox.value.trim().toLowerCase();
    performSearch(input);
});

function performSearch(keyword) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `get_products.php?keyword=${keyword}`, true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            let data = JSON.parse(xhr.responseText);
            display(data);
        } else {
            resultsBox.innerHTML = '<p>Error fetching products.</p>';
        }
    };

    xhr.onerror = function() {
        resultsBox.innerHTML = '<p>Error fetching products.</p>';
    };

    xhr.send();
}

function display(products) {
    if (products.length > 0) {
        let html = '<ul>';
        products.forEach(product => {
            html += `
                <li>
                    <img src="${product.image_path}" alt="${product.name}">
                    ${product.name}
                    <button class="add-button" onclick="selectInput('${product.name}')">+</button>
                </li>
            `;
        });
        html += '</ul>';
        resultsBox.innerHTML = html;
    } else {
        resultsBox.innerHTML = '<p>No products found.</p>';
    }
}

function selectInput(keyword) {
    const isLoggedIn = false; // Replace with actual login check logic

    if (!isLoggedIn) {
        displayModalMessage('Please log in to add items to your cart.');
    } else {
        displayModalMessage(`Added ${keyword} to your cart.`);
    }
}

function displayModalMessage(message) {
    const modalContent = document.querySelector(".modal-content .message");
    modalContent.innerHTML = message;
    const modal = document.getElementById("alertModal");
    modal.style.display = "block";

    const closeModal = document.querySelector(".close");
    closeModal.addEventListener("click", function() {
        modal.style.display = "none";
    });

    window.addEventListener("click", function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
}
