// Global functions
function loadUsers() {
    fetch('php/users/get_users.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#users-table tbody');
            tbody.innerHTML = '';
            
            data.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td>${user.role}</td>
                    <td>${user.status}</td>
                    <td>
                        <button class="btn-edit" onclick="editUser(${user.id})">Edit</button>
                        <button class="btn-delete" onclick="deleteUser(${user.id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error loading users:', error));
}

function loadDeliveries() {
    fetch('php/deliveries/get_deliveries.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#deliveries-table tbody');
            tbody.innerHTML = '';
            
            data.forEach(delivery => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${delivery.tracking_id || 'N/A'}</td>
                    <td>${delivery.sender || 'N/A'}</td>
                    <td>${delivery.recipient || 'N/A'}</td>
                    <td>${delivery.status || 'Pending'}</td>
                    <td>${new Date(delivery.created_at).toLocaleDateString()}</td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error loading deliveries:', error);
            const tbody = document.querySelector('#deliveries-table tbody');
            tbody.innerHTML = '<tr><td colspan="5">Error loading deliveries. Please check console for details.</td></tr>';
        });
}

function loadStores() {
    fetch('php/stores/get_stores.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#stores-table tbody');
            tbody.innerHTML = '';
            
            data.forEach(store => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${store.id}</td>
                    <td>${store.name}</td>
                    <td>${store.address}</td>
                    <td>${store.phone}</td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error loading stores:', error));
}

// User Management Functions
function showAddUserForm() {
    document.getElementById('modal-title').textContent = 'Add New User';
    document.getElementById('user-id').value = '';
    document.getElementById('user-form').reset();
    document.getElementById('user-modal').style.display = 'block';
}

function closeUserModal() {
    document.getElementById('user-modal').style.display = 'none';
}

function editUser(userId) {
    document.getElementById('modal-title').textContent = 'Edit User';
    document.getElementById('user-id').value = userId;
    
    // Fetch user data and populate form
    fetch(`php/users/get_user.php?id=${userId}`)
        .then(response => response.json())
        .then(user => {
            document.getElementById('modal-username').value = user.username;
            document.getElementById('modal-email').value = user.email;
            document.getElementById('modal-role').value = user.role;
            document.getElementById('modal-status').value = user.status;
            document.getElementById('modal-password').value = ''; // Clear password field
            document.getElementById('user-modal').style.display = 'block';
        })
        .catch(error => console.error('Error loading user:', error));
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(`php/users/delete_user.php?id=${userId}`, { 
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                loadUsers(); // Reload the users table
            } else {
                alert('Error deleting user: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error deleting user:', error);
            alert('Error deleting user. Please try again.');
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add back to main site link
    const header = document.querySelector('header');
    const backLink = document.createElement('a');
    backLink.href = 'index.html';
    backLink.className = 'back-link';
    backLink.innerHTML = '← Back to Main Site';
    header.insertBefore(backLink, header.firstChild);

    // Tab switching
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab');
            
            // Update active tab button
            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            // Show selected tab content
            tabContents.forEach(content => {
                content.classList.remove('active');
                if (content.id === `${tabId}-tab`) {
                    content.classList.add('active');
                }
            });
        });
    });

    // Login form handling
    const loginForm = document.getElementById('admin-login-form');
    const loginSection = document.getElementById('login-section');
    const adminDashboard = document.getElementById('admin-dashboard');

    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        // Send login request
        fetch('php/users/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username, password })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loginSection.style.display = 'none';
                adminDashboard.style.display = 'block';
                loadStores();
                loadDeliveries();
                loadUsers();
            } else {
                alert('Login failed: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Login error:', error);
            alert('Login failed. Please try again.');
        });
    });

    // Logout handling
    document.getElementById('logout-btn').addEventListener('click', function() {
        loginSection.style.display = 'block';
        adminDashboard.style.display = 'none';
        loginForm.reset();
    });

    // Search and filter functionality
    const deliverySearch = document.getElementById('delivery-search');
    const deliveryStatus = document.getElementById('delivery-status');

    deliverySearch.addEventListener('input', filterDeliveries);
    deliveryStatus.addEventListener('change', filterDeliveries);

    function filterDeliveries() {
        const searchTerm = deliverySearch.value.toLowerCase();
        const statusFilter = deliveryStatus.value;
        const rows = document.querySelectorAll('#deliveries-table tbody tr');

        rows.forEach(row => {
            const trackingId = row.cells[0].textContent.toLowerCase();
            const sender = row.cells[1].textContent.toLowerCase();
            const receiver = row.cells[2].textContent.toLowerCase();
            const status = row.cells[3].textContent.toLowerCase();

            const matchesSearch = trackingId.includes(searchTerm) || 
                                sender.includes(searchTerm) || 
                                receiver.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter.toLowerCase();

            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
        });
    }

    // Handle user form submission
    document.getElementById('user-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const userId = document.getElementById('user-id').value;
        const userData = {
            username: document.getElementById('modal-username').value,
            email: document.getElementById('modal-email').value,
            password: document.getElementById('modal-password').value,
            role: document.getElementById('modal-role').value,
            status: document.getElementById('modal-status').value
        };

        const url = userId ? `php/users/update_user.php?id=${userId}` : 'php/users/add_user.php';
        const method = userId ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                closeUserModal();
                loadUsers(); // Reload the users table
            } else {
                alert('Error saving user: ' + result.message);
            }
        })
        .catch(error => console.error('Error saving user:', error));
    });
});

// Store management functions
function importStores() {
    window.location.href = 'import_stores.html';
}

function exportStores() {
    // TODO: Implement store export
    console.log('Exporting stores...');
}

function editStore(id) {
    // TODO: Implement store editing
    console.log('Editing store:', id);
}

function deleteStore(id) {
    if (confirm('Are you sure you want to delete this store?')) {
        // TODO: Implement store deletion
        console.log('Deleting store:', id);
    }
}

// Delivery management functions
function viewDelivery(id) {
    // TODO: Implement delivery view
    console.log('Viewing delivery:', id);
}

function updateDeliveryStatus(id) {
    // TODO: Implement status update
    console.log('Updating delivery status:', id);
}

function editDelivery(id) {
    // Fetch delivery data
    fetch(`php/deliveries/get_delivery.php?id=${id}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const delivery = result.data;
                
                // Create and show modal
                const modal = document.createElement('div');
                modal.className = 'modal';
                modal.innerHTML = `
                    <div class="modal-content">
                        <h2>Edit Delivery</h2>
                        <form id="edit-delivery-form">
                            <input type="hidden" id="delivery-id" value="${delivery.id}">
                            <div class="form-group">
                                <label for="tracking-id">Tracking ID</label>
                                <input type="text" id="tracking-id" value="${delivery.tracking_id || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="sender">Sender</label>
                                <input type="text" id="sender" value="${delivery.sender || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="recipient">Recipient</label>
                                <input type="text" id="recipient" value="${delivery.recipient || ''}" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" required>
                                    <option value="Pending" ${delivery.status === 'Pending' ? 'selected' : ''}>Pending</option>
                                    <option value="In Transit" ${delivery.status === 'In Transit' ? 'selected' : ''}>In Transit</option>
                                    <option value="Delivered" ${delivery.status === 'Delivered' ? 'selected' : ''}>Delivered</option>
                                    <option value="Cancelled" ${delivery.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                                </select>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-primary">Save Changes</button>
                                <button type="button" class="btn-secondary" onclick="closeDeliveryModal()">Cancel</button>
                            </div>
                        </form>
                    </div>
                `;
                
                document.body.appendChild(modal);
                
                // Handle form submission
                document.getElementById('edit-delivery-form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const deliveryData = {
                        id: document.getElementById('delivery-id').value,
                        tracking_id: document.getElementById('tracking-id').value,
                        sender: document.getElementById('sender').value,
                        recipient: document.getElementById('recipient').value,
                        status: document.getElementById('status').value
                    };
                    
                    // Send update request
                    fetch('php/deliveries/update_delivery.php', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(deliveryData)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            closeDeliveryModal();
                            loadDeliveries(); // Reload the deliveries table
                        } else {
                            alert('Error updating delivery: ' + result.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating delivery:', error);
                        alert('Error updating delivery. Please try again.');
                    });
                });
            } else {
                alert('Error loading delivery: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error loading delivery:', error);
            alert('Error loading delivery. Please try again.');
        });
}

function closeDeliveryModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}

function deleteDelivery(id) {
    if (confirm('Are you sure you want to delete this delivery?')) {
        fetch(`php/deliveries/delete_delivery.php?id=${id}`, { 
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadDeliveries(); // Reload the deliveries table
            } else {
                alert('Error deleting delivery: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error deleting delivery:', error);
            alert('Error deleting delivery. Please try again.');
        });
    }
} 