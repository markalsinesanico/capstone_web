<template>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <nav class="menu">
        <router-link to="/dashboard">DASHBOARD</router-link>
        <router-link to="/borrowers">BORROWERS</router-link>
        <router-link to="/appointment">APPOINTMENT</router-link>
        <router-link to="/history">HISTORY</router-link>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="main">
      <!-- Topbar -->
      <header class="topbar">
        <div class="logo">
          <img src="/img/logo.png" alt="Logo" />
          <div class="logo-text">
            <h2>SMART LINK</h2>
            <p>APPOINT & BORROW</p>
          </div>
        </div>
        <div class="user">M {{ userEmail }}</div>
      </header>

      <!-- Item Section -->
      <section class="item-section">
        <div class="item-header">
          <h2>Available Items</h2>
          <button @click="openModal('add')">+ Add</button>
        </div>
        <div class="item-list">
          <div v-for="item in items" :key="item.id" class="item">
            <div class="item-info">
              <img :src="getImageUrl(item)" alt="" />
              <div>
                <div class="item-name">{{ item.name }}</div>
                <div class="item-quantity">Qty: {{ item.qty }}</div>
                <div class="item-description">{{ item.description }}</div>
              </div>
            </div>
            <div class="item-actions">
              <button class="edit-btn" @click="openModal('edit', item)">EDIT</button>
              <button class="request-btn" @click="openModal('request', item)">Request ITEM</button>
              <button class="delete-btn" @click="deleteItem(item.id)">DELETE</button>
            </div>
          </div>
        </div>
      </section>

      <!-- Borrower Table -->
      <section class="borrower-section">
        <div class="search-bar">
          <input v-model="search" placeholder="Search Borrowers..." />
        </div>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>ID</th>
              <th>Date</th>
              <th>Time In</th>
              <th>Time Out</th>
              <th>Item</th>
              <th>Status</th>
              <!-- <th>Action</th> -->
            </tr>
          </thead>
          <tbody>
            <tr v-for="(borrower, index) in filteredBorrowers" :key="borrower.id">
              <td>{{ index + 1 }}</td>
              <td>{{ borrower.name }}</td>
              <td>{{ borrower.borrower_id }}</td>
              <td>{{ borrower.date }}</td>
              <td>{{ borrower.time_in }}</td>
              <td>{{ borrower.time_out }}</td>
              <td>{{ borrower.item?.name || 'N/A' }}</td>
              <td>{{ borrower.status }}</td>
              <!-- <td>
                <button class="delete-btn" @click="deleteBorrower(index)">Delete</button>
              </td> -->
            </tr>
          </tbody>
        </table>
      </section>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal" @click.self="closeModal">
      <div class="modal-content">
        <span class="close" @click="closeModal">&times;</span>
        <h3>{{ modalTitle }}</h3>
        <form @submit.prevent="handleSubmit">
          <div class="modal-form">
            <!-- Add/Edit Item -->
            <template v-if="modalType === 'add' || modalType === 'edit'">
              <label>
                <span>📦 Item Name</span>
                <input v-model="form.name" placeholder="Enter item name" required />
              </label>
              <label>
                <span>🔢 Quantity</span>
                <input v-model.number="form.qty" type="number" placeholder="Enter quantity" required />
              </label>
              <label>
                <span>🖼️ Upload Image</span>
                <input type="file" accept="image/*" @change="handleImageUpload" />
                <img v-if="form.imagePreview" :src="form.imagePreview" alt="Preview" class="image-preview" />
              </label>
              <label>
                <span>📝 Description</span>
                <textarea v-model="form.description" placeholder="Enter description"></textarea>
              </label>
            </template>

            <!-- Request Item -->
            <template v-else-if="modalType === 'request'">
              <label>
                <span>👤 Full Name</span>
                <input v-model="form.name" placeholder="Enter your name" required />
              </label>
              <label>
                <span>🆔 ID Number</span>
                <input v-model="form.id" placeholder="Enter ID number" required />
              </label>
              <label>
                <span>📅 Date</span>
                <input v-model="form.date" type="date" required />
              </label>
              <label>
                <span>⏰ Time In</span>
                <input v-model="form.timeIn" type="time" required />
              </label>
              <label>
                <span>⏳ Time Out</span>
                <input v-model="form.timeOut" type="time" required />
              </label>
              <label>
                <span>🧰 Item</span>
                <input v-model="form.item" readonly />
              </label>
            </template>
          </div>
          <button type="submit" class="submit-btn">✔ Submit</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "Dashboard",
  data() {
    return {
      userEmail: "msanico@ssct.edu.ph",
      items: [],
      borrowers: [],
      search: "",
      showModal: false,
      modalType: "",
      currentItem: null,
      form: {},
      loading: false,
    };
  },
  computed: {
    modalTitle() {
      return {
        add: "Add New Item",
        edit: "Edit Item",
        request: "Request Item",
      }[this.modalType];
    },
    filteredBorrowers() {
      return this.borrowers.filter(b =>
        b.name?.toLowerCase().includes(this.search.toLowerCase())
      );
    },
  },
  mounted() {
    this.fetchItems();
    this.fetchBorrowers(); // Fetch requests from backend
  },
  methods: {
    async fetchItems() {
      this.loading = true;
      try {
        const res = await axios.get("/api/items");
        this.items = res.data;
      } catch (e) {
        alert("Failed to fetch items.");
      }
      this.loading = false;
    },
    async fetchBorrowers() {
      try {
        const res = await axios.get("/api/requests");
        this.borrowers = res.data;
      } catch (e) {
        alert("Failed to fetch requests.");
      }
    },
    openModal(type, item = null) {
      this.modalType = type;
      this.currentItem = item;
      this.showModal = true;

      if (type === "edit" && item) {
        this.form = { ...item, image: null }; // image will be handled separately
      } else if (type === "request") {
        this.form = {
          name: "",
          id: "",
          date: "",
          timeIn: "",
          timeOut: "",
          item: item?.name || "",
        };
      } else {
        this.form = { name: "", qty: "", image: null, description: "" };
      }
    },
    closeModal() {
      this.showModal = false;
      this.form = {};
      this.currentItem = null;
    },
    async handleSubmit() {
      if (this.modalType === "add") {
        await this.addItem();
      } else if (this.modalType === "edit") {
        await this.editItem();
      } else if (this.modalType === "request") {
        try {
          // Find the item by name to get its ID
          const item = this.items.find(i => i.name === this.form.item);
          if (!item) throw new Error("Item not found.");

          await axios.post("/api/requests", {
            name: this.form.name,
            borrower_id: this.form.id, // <-- Make sure this is 'borrower_id'
            date: this.form.date,
            time_in: this.form.timeIn,
            time_out: this.form.timeOut,
            item_id: item.id, // <-- Send the item ID
          });
          this.closeModal();
          alert("Request submitted!");
        } catch (e) {
          // Show backend error message if available
          if (e.response && e.response.data && e.response.data.message) {
            alert("Failed to submit request: " + e.response.data.message);
          } else if (e.response && e.response.data && e.response.data.errors) {
            alert("Failed to submit request: " + JSON.stringify(e.response.data.errors));
          } else {
            alert("Failed to submit request.");
          }
          console.error(e);
        }
      }
    },
    async addItem() {
      try {
        const formData = new FormData();
        formData.append("name", this.form.name);
        formData.append("qty", this.form.qty);
        formData.append("description", this.form.description || "");
        if (this.form.image instanceof File) {
          formData.append("image", this.form.image);
        }
        await axios.post("/api/items", formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        this.fetchItems();
        this.closeModal();
      } catch (e) {
        // Show backend error message if available
        if (e.response && e.response.data && e.response.data.message) {
          alert("Failed to add item: " + e.response.data.message);
        } else if (e.response && e.response.data && e.response.data.errors) {
          alert("Failed to add item: " + JSON.stringify(e.response.data.errors));
        } else {
          alert("Failed to add item.");
        }
        console.error(e);
      }
    },
    async editItem() {
      try {
        const formData = new FormData();
        formData.append("name", this.form.name);
        formData.append("qty", this.form.qty);
        formData.append("description", this.form.description || "");
        if (this.form.image instanceof File) {
          formData.append("image", this.form.image);
        }
        await axios.post(`/api/items/${this.currentItem.id}?_method=PUT`, formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        this.fetchItems();
        this.closeModal();
      } catch (e) {
        alert("Failed to update item.");
      }
    },
    handleImageUpload(event) {
      const file = event.target.files[0];
      if (file && file.type.startsWith("image/")) {
        this.form.image = file;
        // For preview
        const reader = new FileReader();
        reader.onload = e => {
          this.form.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
      } else {
        alert("Please upload a valid image file.");
      }
    },
    async deleteItem(id) {
      if (!confirm("Are you sure you want to delete this item?")) return;
      try {
        await axios.delete(`/api/items/${id}`);
        this.fetchItems();
      } catch (e) {
        alert("Failed to delete item.");
      }
    },
    async deleteBorrower(index) {
      const borrower = this.filteredBorrowers[index];
      if (!borrower || !borrower.id) return;
      if (!confirm("Are you sure you want to delete this request?")) return;
      try {
        await axios.delete(`/api/requests/${borrower.id}`);
        this.fetchBorrowers();
      } catch (e) {
        alert("Failed to delete request.");
      }
    },
    getImageUrl(item) {
      return item.image_url || "/img/no-image.png";
    },
  },
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
  min-width: 100vw;
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
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background-color: white;
      padding: 30px 24px 24px 24px;
      border-radius: 14px;
      width: 400px;
      position: relative;
      box-shadow: 0 4px 24px rgba(0,0,0,0.18);
    }

    .close {
      position: absolute;
      right: 20px;
      top: 10px;
      font-size: 22px;
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
  margin-bottom: 16px;
  font-size: 15px;
  color: #333;
}

.modal-form label span {
  margin-bottom: 5px;
  font-weight: bold;
}

.modal-form input, .modal-form select, .modal-form textarea {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 15px;
}

.image-preview {
  margin-top: 8px;
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
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
