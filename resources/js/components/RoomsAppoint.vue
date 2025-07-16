<template>
    <div class="container">
        <aside class="sidebar">
      <nav class="menu">
        <router-link to="/">DASHBOARD</router-link>
        <router-link to="/borrowers">BORROWERS</router-link>
        <router-link to="/appointment">APPOINTMENT</router-link>
        <router-link to="/history">HISTORY</router-link>
      </nav>
    </aside>
  
      <main class="main">
        <header class="topbar">
          <div class="logo">
            <img src="img/logo.png" alt="Logo"/>
            <div class="logo-text">
              <h2>SMART LINK</h2>
              <p>APPOINT & BORROW</p>
            </div>
          </div>
          <div class="user">M msanico@ssct...</div>
        </header>
  
        <section class="item-section">
          <div class="item-header">
            <h2>Available Rooms</h2>
            <button @click="addRoom">+ Add Room</button>
          </div>
          <div class="item-list">
            <div class="item" v-for="(room, index) in rooms" :key="index">
              <div class="item-info">
                <div>
                  <div class="item-name">{{ room.name }}</div>
                  <div class="item-quantity">{{ room.quantity }}</div>
                </div>
              </div>
              <div class="item-actions">
                <button class="edit-btn" @click="editRoom(index)">EDIT</button>
                <button class="request-btn" @click="requestRoom(room)">Request Room</button>
                <button class="delete-btn" @click="deleteRoom(index)">DELETE</button>
              </div>
            </div>
          </div>
        </section>
  
        <section class="borrower-section">
          <div class="search-bar">
            <input type="text" placeholder="Search..." v-model="search" />
          </div>
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>ID number</th>
                <th>Type</th>
                <th>Year</th>
                <th>Department</th>
                <th>Course</th>
                <th>Date</th>
                <th>IN</th>
                <th>OUT</th>
                <th>Account</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Jomong</td>
                <td><a href="#">2022-295</a></td>
                <td>Computer Lab</td>
                <td>3rd yr</td>
                <td>CEIT</td>
                <td>BSICT</td>
                <td>06/05/2025</td>
                <td>11:30 AM</td>
                <td>12:30 PM</td>
                <td>msanico@ssct.edu.ph</td>
                <td>
                  <button class="status-btn approve-btn">APPROVE</button>
                  <button class="status-btn cancel-btn">CANCEL</button>
                </td>
              </tr>
            </tbody>
          </table>
        </section>
      </main>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        rooms: [{ name: 'COMPUTER LAB', quantity: 5 }],
        search: ''
      };
    },
    methods: {
      addRoom() {
        const name = prompt('Enter room name:');
        const quantity = prompt('Enter quantity:');
        if (name && quantity) {
          this.rooms.push({ name, quantity: parseInt(quantity) });
        }
      },
      editRoom(index) {
        const updatedName = prompt('Update room name:', this.rooms[index].name);
        const updatedQuantity = prompt('Update quantity:', this.rooms[index].quantity);
        if (updatedName && updatedQuantity) {
          this.rooms[index].name = updatedName;
          this.rooms[index].quantity = parseInt(updatedQuantity);
        }
      },
      requestRoom(room) {
        alert(`Requesting room: ${room.name}`);
      },
      deleteRoom(index) {
        if (confirm('Are you sure you want to delete this room?')) {
          this.rooms.splice(index, 1);
        }
      }
    }
  };
  </script>
  
  <style scoped>
 
 * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Roboto', sans-serif;
    }

    body {
      background: #f5f5f5;
    }

    .container {
      display: flex;
      flex-direction: row;
      min-height: 100vh;
    }

    .sidebar {
  width: 220px;
  background: #2c3e50;
  color: white;
  display: flex;
  flex-direction: column;
  padding: 30px 20px;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

.menu {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-top: 20px;
}

.menu a {
  color: white;
  text-decoration: none;
  font-size: 16px;
  padding: 10px 15px;
  border-radius: 8px;
  transition: background 0.3s, color 0.3s;
}

.menu a.active,
.menu a:hover {
  background-color: #18bc9c;
  color: #ffffff;
}

    .main {
      flex: 1;
      padding: 20px;
      display: flex;
      flex-direction: column;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      background-color: #007e3a;
      padding: 10px 20px;
      color: white;
      border-radius: 8px;
    }

    .topbar .logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .topbar .logo img {
      height: 50px;
    }

    .topbar .logo-text h2 {
      font-size: 18px;
      color: white;
      margin: 0;
    }

    .topbar .logo-text p {
      font-size: 12px;
      color: white;
      margin: 0;
    }

    .topbar .user {
      background: rgba(255, 255, 255, 0.2);
      padding: 8px 12px;
      border-radius: 20px;
      color: white;
      font-weight: bold;
    }

    .item-section, .borrower-section {
      margin-top: 20px;
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .item-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .item-header button {
      padding: 8px 16px;
      background: #2ecc71;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .item-list {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px;
      border-bottom: 1px solid #eee;
    }

    .item img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 8px;
    }

    .item-info {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .item-name {
      font-size: 20px;
      font-weight: bold;
    }

    .item-quantity {
      color: green;
      font-size: 18px;
    }

    .item-actions button {
      margin: 0 5px;
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      color: white;
      cursor: pointer;
    }

    .edit-btn { background-color: #007bff; }
    .request-btn { background-color: #28a745; }
    .delete-btn { background-color: #dc3545; }

    .borrower-section .search-bar {
      margin-bottom: 15px;
      display: flex;
      justify-content: flex-end;
    }

    .search-bar input {
      padding: 8px 12px;
      width: 300px;
      border-radius: 20px;
      border: 1px solid #ccc;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    table th, table td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }

    .status-btn {
      padding: 6px 10px;
      border: none;
      border-radius: 5px;
      color: white;
      cursor: pointer;
    }

    .approve-btn { background-color: #28a745; }
    .cancel-btn { background-color: #dc3545; }

    .modal {
     
      position: fixed;
      z-index: 999;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
      background-color: white;
      margin: 10% auto;
      padding: 20px;
      border-radius: 10px;
      width: 400px;
      position: relative;
    }

    .close {
      position: absolute;
      right: 20px;
      top: 10px;
      font-size: 20px;
      cursor: pointer;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }

      .sidebar {
        flex-direction: row;
        width: 100%;
        padding: 10px;
        justify-content: space-around;
      }

      .menu {
        display: flex;
        gap: 10px;
      }

      .menu a {
        font-size: 14px;
        padding: 6px 10px;
      }

      .topbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
    }
    .modal-form label {
  display: flex;
  flex-direction: column;
  margin-bottom: 12px;
  font-size: 14px;
  color: #333;
}

.modal-form label span {
  margin-bottom: 5px;
  font-weight: bold;
}

.modal-form input {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

.submit-btn {
  background-color: #007e3a;
  color: white;
  padding: 10px 16px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
  width: 100%;
  margin-top: 10px;
  transition: background 0.3s ease;
}

.submit-btn:hover {
  background-color: #00632e;
}
</style>