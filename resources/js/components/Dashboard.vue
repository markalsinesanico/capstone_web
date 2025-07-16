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
          <img src="/img/logo.png" alt="Logo" />
          <div class="logo-text">
            <h2>SMART LINK</h2>
            <p>APPOINT & BORROW</p>
          </div>
        </div>
        <div class="user">M msanico@ssct...</div>
      </header>

      <section class="stats">
        <div class="card">Number Of Borrowers<br><span>27</span></div>
        <div class="card">Number Of Rooms<br><span>3</span></div>
        <div class="card">Number Of Item<br><span>4</span></div>
      </section>

      <section class="content">
        <div class="calendar">
          <h3>Select date</h3>
          <input type="date" v-model="selectedDate" @change="updateAvailableItems" />
          <h4 style="margin-top: 10px;">Available Items</h4>
          <div id="availableItemsList">
            <div v-if="availableItems.length === 0"><p>No items available for this date.</p></div>
            <div
              v-for="(item, index) in availableItems"
              :key="index"
              class="item-card"
            >
              <img :src="item.image" :alt="item.name" />
              <h4>{{ item.name }}</h4>
              <p>Available Slots: {{ item.slots }}</p>
            </div>
          </div>
        </div>

        <div class="schedule">
          <div class="filter-search">
            <label for="sort">Sort By:</label>
            <select id="sort">
              <option>EQUIPMENT</option>
              <option>ROOMS</option>
            </select>
            <input type="text" placeholder="Search" />
          </div>

          <div class="calendar-grid">
            <div v-for="(time, index) in timeSlots" :key="index" class="time-slot">{{ time }}</div>
            <div
              v-for="(event, index) in events"
              :key="index"
              class="event"
              :style="{
                gridColumn: event.col,
                gridRow: `${event.rowStart} / span ${event.rowSpan}`,
                background: event.color
              }"
              @click="showDetails(
                event.name,
                event.idnum,
                event.year,
                event.dept,
                event.course,
                event.datetime
              )"
            >
              <strong>{{ event.name }}</strong>
              <p>{{ event.item }}</p>
              <br />
              <small>{{ getTimeRange(event.rowStart, event.rowSpan) }}</small>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- Modal -->
    <div class="modal" v-if="modalVisible" @click.self="closeModal">
      <div class="modal-content">
        <h3>Event Details</h3>
        <p><strong>Full Name:</strong> {{ modalData.name }}</p>
        <p><strong>ID Number:</strong> {{ modalData.idnum }}</p>
        <p><strong>School Year:</strong> {{ modalData.year }}</p>
        <p><strong>Department:</strong> {{ modalData.dept }}</p>
        <p><strong>Course:</strong> {{ modalData.course }}</p>
        <p><strong>Date & Time:</strong> {{ modalData.datetime }}</p>
        <button class="close-btn" @click="closeModal">Close</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Dashboard",
  data() {
    return {
      selectedDate: "2025-08-17",
      modalVisible: false,
      modalData: {
        name: "",
        idnum: "",
        year: "",
        dept: "",
        course: "",
        datetime: "",
      },
      timeSlots: [
        "7 AM", "8 AM", "9 AM", "10 AM", "11 AM",
        "12 PM", "1 PM", "2 PM", "3 PM", "4 PM", "5 PM",
      ],
      itemsByDate: {
        "2025-08-17": [
          { name: "Projector", image: "/img/projector.png", slots: 3 },
          { name: "HDMI", image: "/img/hdmi.png", slots: 5 },
        ],
        "2025-08-18": [
          { name: "Speaker", image: "/img/speaker.png", slots: 2 },
          { name: "Remote", image: "/img/remote.png", slots: 4 },
        ],
        "2025-08-19": [
          { name: "Projector", image: "/img/projector.png", slots: 1 },
          { name: "Speaker", image: "/img/speaker.png", slots: 0 },
          { name: "HDMI", image: "/img/hdmi.png", slots: 2 },
          { name: "Remote", image: "/img/remote.png", slots: 1 },
        ],
      },
      availableItems: [],
      events: [
        {
          name: 'John Doe', idnum: '2023001234', year: '2024–2025', dept: 'CEIT', course: 'BSIT',
          datetime: '2025-08-17 08:00', item: 'Projector', col: 2, rowStart: 2, rowSpan: 2, color: '#aed6f1'
        },
        {
          name: 'Maria Reyes', idnum: '2023005678', year: '2024–2025', dept: 'CBA', course: 'BSA',
          datetime: '2025-08-17 08:30', item: 'Projector', col: 3, rowStart: 2, rowSpan: 1, color: '#d2b4de'
        },
        {
          name: 'Kevin Cruz', idnum: '2023011122', year: '2024–2025', dept: 'CEIT', course: 'BSCS',
          datetime: '2025-08-17 11:00', item: 'HDMI', col: 2, rowStart: 5, rowSpan: 2, color: '#f5b7b1'
        },
        // Additional 10+ requests can be added here
      ]
    };
  },
  mounted() {
    this.updateAvailableItems();
  },
  methods: {
    updateAvailableItems() {
      this.availableItems = this.itemsByDate[this.selectedDate] || [];
    },
    showDetails(name, idnum, year, dept, course, datetime) {
      this.modalData = { name, idnum, year, dept, course, datetime };
      this.modalVisible = true;
    },
    closeModal() {
      this.modalVisible = false;
    },
    getTimeRange(rowStart, rowSpan) {
      const hourMap = [
        "7:00 AM", "8:00 AM", "9:00 AM", "10:00 AM", "11:00 AM",
        "12:00 PM", "1:00 PM", "2:00 PM", "3:00 PM", "4:00 PM", "5:00 PM"
      ];
      const start = hourMap[rowStart - 1];
      const endIndex = rowStart - 1 + rowSpan;
      const end = hourMap[endIndex] || "5:00 PM";
      return `${start}–${end}`;
    }
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

.stats {
  display: flex;
  gap: 20px;
  margin-top: 20px;
  flex-wrap: wrap;
}

.card {
  background: white;
  padding: 20px;
  border-radius: 10px;
  flex: 1;
  min-width: 150px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.card span {
  font-size: 24px;
  color: #2ecc71;
}

.content {
  display: flex;
  margin-top: 30px;
  gap: 20px;
  flex-wrap: wrap;
}

.calendar {
  background: #f1eaff;
  padding: 15px;
  border-radius: 10px;
  text-align: center;
  flex: 1;
  min-width: 200px;
}

.calendar input {
  margin-top: 10px;
  padding: 6px;
  border-radius: 5px;
  border: 1px solid #ccc;
  width: 100%;
}

.schedule {
  flex: 3;
  min-width: 280px;
}

.filter-search {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 15px;
}

.filter-search select,
.filter-search input {
  padding: 8px;
  border-radius: 5px;
  border: 1px solid #ccc;
  flex: 1;
  min-width: 130px;
}

.calendar-grid {
  display: grid;
  grid-template-columns: 100px repeat(2, 1fr);
  grid-template-rows: repeat(11, 60px);
  position: relative;
  background: white;
  border: 1px solid #ccc;
  border-radius: 10px;
  overflow: hidden;
}

.time-slot {
  grid-column: 1;
  border-bottom: 1px solid #ddd;
  padding: 10px;
  font-size: 13px;
  color: #555;
}

.event {
  padding: 6px;
  font-size: 12px;
  color: #000;
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.1);
  margin: 2px;
  cursor: pointer;
}

.modal {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background-color: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 10px;
  width: 300px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.modal-content h3 {
  margin-bottom: 10px;
}

.modal-content p {
  font-size: 14px;
  margin-bottom: 5px;
}

.close-btn {
  margin-top: 10px;
  padding: 8px 12px;
  background: #2ecc71;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.item-card {
  background: white;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 10px;
  margin-top: 10px;
  text-align: center;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.item-card img {
  max-width: 80px;
  height: auto;
  margin-bottom: 5px;
}

.item-card h4 {
  margin: 5px 0;
  font-size: 14px;
}

.item-card p {
  font-size: 12px;
  color: #555;
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
  .stats, .content, .filter-search {
    flex-direction: column;
  }
  .calendar, .schedule {
    width: 100%;
  }
}
</style>
