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
            <!-- Time labels -->
            <div
              v-for="(time, i) in timeSlots"
              :key="'time-' + i"
              class="time-label"
              :style="{ gridRow: i + 2, gridColumn: 1 }"
            >
              {{ time }}
            </div>
            <!-- Day headers -->
            <div
              v-for="(day, i) in days"
              :key="'day-' + i"
              class="day-header"
              :style="{ gridRow: 1, gridColumn: i + 2 }"
            >
              {{ day }}
            </div>
            <!-- Events -->
            <div
              v-for="(event, i) in processedEvents"
              :key="'event-' + i"
              class="event-block"
              :style="{
                gridColumn: event.col,
                gridRow: event.rowStart + ' / span ' + event.rowSpan,
                background: event.color
              }"
              @click="showDetails(event.name, event.idnum, event.year, event.dept, event.course, event.startTime + ' - ' + event.endTime, event.startTime, event.endTime, event.type || 'N/A', event.account || 'N/A')"
            >
              <strong>{{ event.name }}</strong>
              <div>{{ formatTime(event.startTime) }} - {{ formatTime(event.endTime) }}</div>
              <div>{{ event.item }}</div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- Modal -->
    <div class="modal" v-if="modalVisible" @click.self="closeModal">
      <div class="modal-content">
        <button class="close-x-btn" @click="closeModal" aria-label="Close">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="4" x2="16" y2="16"/><line x1="16" y1="4" x2="4" y2="16"/></svg>
        </button>
        <h3>Event Details</h3>
        <table class="event-details-table">
          <tr><td><strong>Name:</strong></td><td>{{ modalData.name }}</td></tr>
          <tr><td><strong>ID number:</strong></td><td>{{ modalData.idnum }}</td></tr>
          <tr><td><strong>Type:</strong></td><td>{{ modalData.type }}</td></tr>
          <tr><td><strong>Year:</strong></td><td>{{ modalData.year }}</td></tr>
          <tr><td><strong>Department:</strong></td><td>{{ modalData.dept }}</td></tr>
          <tr><td><strong>Course:</strong></td><td>{{ modalData.course }}</td></tr>
          <tr><td><strong>Date:</strong></td><td>{{ modalData.date }}</td></tr>
          <tr><td><strong>IN:</strong></td><td>{{ modalData.in }}</td></tr>
          <tr><td><strong>OUT:</strong></td><td>{{ modalData.out }}</td></tr>
          <tr><td><strong>Account:</strong></td><td>{{ modalData.account }}</td></tr>
        </table>
      </div>
    </div>

    <!-- Floating QR Scan Button -->
    <button class="qr-fab" @click="openQrModal">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
    </button>
    <!-- QR Code Modal -->
    <div class="qr-modal" v-if="showQrModal" @click.self="closeQrModal">
      <div class="qr-modal-content">
        <h3>Scan QR Code</h3>
        <qrcode-stream @decode="onDecode" />
        <div v-if="qrResult">
          <p><strong>Result:</strong> {{ qrResult }}</p>
        </div>
        <button class="close-btn" @click="closeQrModal">Close</button>
      </div>
    </div>
  </div>
</template>

<script>
import { QrcodeStream } from 'vue-qrcode-reader';
export default {
  components: { QrcodeStream },
  data() {
    return {
      selectedDate: new Date().toISOString().substr(0, 10),
      modalVisible: false,
      modalData: {
        name: '',
        idnum: '',
        type: '', // new
        year: '',
        dept: '',
        course: '',
        date: '', // new
        in: '',   // new
        out: '',  // new
        account: '' // new
      },
      availableItems: [],
      timeSlots: [
        "7:00 AM", "7:30 AM", "8:00 AM", "8:30 AM", "9:00 AM", "9:30 AM",
        "10:00 AM", "10:30 AM", "11:00 AM", "11:30 AM", "12:00 PM", "12:30 PM",
        "1:00 PM", "1:30 PM", "2:00 PM", "2:30 PM", "3:00 PM", "3:30 PM",
        "4:00 PM", "4:30 PM", "5:00 PM", "5:30 PM", "6:00 PM", "6:30 PM"
      ],
     
      events: [
        {
          name: 'Leo Santos',
          idnum: '2023123002',
          year: '2024–2025',
          dept: 'CBA',
          course: 'BSA',
          startTime: '2025-08-17 07:00',
          endTime: '2025-08-17 08:30',
          item: 'Projector',
          color: '#43a047' // green
        },
        {
          name: 'Ana Cruz',
          idnum: '2023123001',
          year: '2024–2025',
          dept: 'CEIT',
          course: 'BSIT',
          startTime: '2025-08-17 08:00',
          endTime: '2025-08-17 09:30',
          item: 'Speaker',
          color: '#e53935' // red
        },
        // Additional random events
        {
          name: 'Carlos Reyes',
          idnum: '2023123004',
          year: '2024–2025',
          dept: 'CAS',
          course: 'BAEL',
          startTime: '2025-08-17 07:00',
          endTime: '2025-08-17 08:30',
          item: 'Microphone',
          color: '#1e88e5' // blue
        },
        {
          name: 'Diana Lim',
          idnum: '2023123005',
          year: '2024–2025',
          dept: 'COE',
          course: 'BSEE',
          startTime: '2025-08-17 09:00',
          endTime: '2025-08-17 14:00',
          item: 'Remote',
          color: '#fbc02d' // yellow
        },
        {
          name: 'Evan Yu',
          idnum: '2023123006',
          year: '2024–2025',
          dept: 'CBA',
          course: 'BSBA',
          startTime: '2025-08-17 10:00',
          endTime: '2025-08-17 15:00',
          item: 'Projector',
          color: '#8e24aa' // purple
        },
        {
          name: 'Faye Gomez',
          idnum: '2023123007',
          year: '2024–2025',
          dept: 'CEIT',
          course: 'BSIT',
          startTime: '2025-08-17 07:00',
          endTime: '2025-08-17 08:30',
          item: 'Speaker',
          color: '#00acc1' // teal
        },
        {
          name: 'George Tan',
          idnum: '2023123008',
          year: '2024–2025',
          dept: 'CTE',
          course: 'BSE',
          startTime: '2025-08-17 12:00',
          endTime: '2025-08-17 17:00',
          item: 'Remote',
          color: '#ff7043' // orange
        }
      ],
      showQrModal: false,
      qrResult: ''
    };
  },
  computed: {
    processedEvents() {
      // Only for the selected date
      const eventsForDay = this.events.filter(e =>
        e.startTime.startsWith(this.selectedDate)
      );
      const eventsToShow = eventsForDay.length > 0 ? eventsForDay : this.events;

      // Sort by start time
      const sorted = [...eventsToShow].sort((a, b) =>
        a.startTime.localeCompare(b.startTime)
      );

      // Assign columns based on overlap
      const columns = [];
      const result = [];

      sorted.forEach(event => {
        const rowStart = this.getRowIndex(event.startTime.split(' ')[1]);
        const rowEnd = this.getRowIndex(event.endTime.split(' ')[1]);
        const rowSpan = rowEnd - rowStart + 1; // <-- add +1 here!

        // Find the first available column
        let col = 2; // gridColumn starts at 2
        while (
          columns[col] &&
          columns[col].some(
            e =>
              // overlap if start < e.end && end > e.start
              rowStart < e.end && rowEnd > e.start
          )
        ) {
          col++;
        }
        // Register this event in the column
        if (!columns[col]) columns[col] = [];
        columns[col].push({ start: rowStart, end: rowEnd });

        result.push({
          ...event,
          rowStart: rowStart,
          rowSpan: rowSpan,
          col
        });
      });

      return result;
    }
  },
  methods: {
    getRowIndex(time) {
      const [hour, minute] = time.split(":").map(Number);
      // 7:00 AM is row 2 (since row 1 is header), 7:30 is row 3, 8:00 is row 4, etc.
      return (hour - 7) * 2 + (minute === 30 ? 3 : 2);
    },
    getTimeRange(startRow, span) {
      const startIndex = startRow - 2; // since row 2 is index 0 in timeSlots
      const endIndex = startIndex + span;
      return `${this.timeSlots[startIndex]} - ${this.timeSlots[endIndex] || ''}`;
    },
    formatTime(datetime) {
      // Expects 'YYYY-MM-DD HH:mm'
      const time = datetime.split(' ')[1];
      let [hour, minute] = time.split(':').map(Number);
      const ampm = hour >= 12 ? 'PM' : 'AM';
      hour = hour % 12 || 12;
      return `${hour}:${minute.toString().padStart(2, '0')} ${ampm}`;
    },
    showDetails(name, idnum, year, dept, course, datetime, startTime, endTime, type = 'N/A', account = 'N/A') {
      // Extract date, in, out from startTime/endTime
      const date = startTime.split(' ')[0];
      const inTime = startTime.split(' ')[1];
      const outTime = endTime.split(' ')[1];
      console.log('showDetails called', { name, idnum, year, dept, course, datetime, startTime, endTime, type, account });
      this.modalData = { name, idnum, type, year, dept, course, date, in: inTime, out: outTime, account };
      this.modalVisible = true;
    },
    closeModal() {
      this.modalVisible = false;
    },
    updateAvailableItems() {
      this.availableItems = [
        { name: 'Projector', slots: 2, image: '/img/projector.png' },
        { name: 'Speaker', slots: 1, image: '/img/speaker.png' },
        { name: 'Room A', slots: 1, image: '/img/room.png' }
      ];
    },
    onDecode(result) {
      this.qrResult = result;
    },
    openQrModal() {
      this.qrResult = '';
      this.showQrModal = true;
    },
    closeQrModal() {
      this.showQrModal = false;
    }
  },
  mounted() {
    this.updateAvailableItems();
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
  flex-shrink: 0;
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
  width: 100%;
  margin-left: 0;
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
  grid-template-columns: 120px repeat(5, 1fr);
  grid-template-rows: 40px repeat(26, 32px); /* Adjust for more time slots if needed */
  background: #fff;
  border: 1px solid #ccc;
}

.time-label, .day-header {
  border-bottom: 1px solid #e0e0e0;
  border-right: 1px solid #ccc;
  background: #fff;
  font-size: 13px;
  font-weight: 500;
  text-align: right;
  padding: 0 10px;
  line-height: 32px;
}

.day-header {
  background: #f5f5f5;
  color: #333;
  text-align: center;
  font-weight: bold;
  border-bottom: 2px solid #ccc;
  border-right: 1px solid #ccc;
  font-size: 15px;
  line-height: 40px;
}

.calendar-grid > div:not(.day-header):not(.time-label):not(.event-block) {
  background: #fff;
  border-bottom: 1px solid #e0e0e0;
  border-right: 1px solid #eee;
}

.event-block {
  color: #fff;
  border-radius: 6px;
  padding: 8px 6px;
  font-size: 14px;
  border: none;
  box-sizing: border-box;
  margin: 2px 4px;
  z-index: 2;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  font-weight: bold;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  cursor: pointer;
  line-height: 1.2;
}

.modal {
  display: flex !important;
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background-color: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
  z-index: 2000;
}
.close-x-btn {
  position: absolute;
  top: 12px;
  right: 12px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 4px;
  z-index: 10;
}
.close-x-btn svg {
  display: block;
  stroke: #333;
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

.qr-fab {
  position: fixed;
  bottom: 32px;
  right: 32px;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #007e3a;
  color: #fff;
  border: none;
  box-shadow: 0 2px 8px rgba(0,0,0,0.18);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  z-index: 1001;
  cursor: pointer;
  transition: background 0.2s;
}
.qr-fab:hover {
  background: #18bc9c;
}
.qr-modal {
  position: fixed;
  top: 0; left: 0; width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1002;
}
.qr-modal-content {
  background: #fff;
  padding: 24px 20px 16px 20px;
  border-radius: 12px;
  min-width: 320px;
  max-width: 90vw;
  box-shadow: 0 4px 16px rgba(0,0,0,0.18);
  text-align: center;
}
.qr-modal-content h3 {
  margin-bottom: 12px;
}
.qr-modal-content p {
  margin: 10px 0 0 0;
}
.close-btn {
  margin-top: 16px;
  padding: 8px 16px;
  background: #2ecc71;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.event-details-table {
  width: 100%;
  margin-bottom: 16px;
  border-collapse: collapse;
}
.event-details-table td {
  padding: 4px 8px;
  font-size: 14px;
}
.event-modal-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  margin-top: 10px;
}
.approve-btn {
  background: #2ecc71;
  color: #fff;
  border: none;
  border-radius: 5px;
  padding: 8px 14px;
  cursor: pointer;
  font-weight: bold;
}
.reject-btn {
  background: #e74c3c;
  color: #fff;
  border: none;
  border-radius: 5px;
  padding: 8px 14px;
  cursor: pointer;
  font-weight: bold;
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
  .event {
  position: relative;
  padding: 4px;
  margin-top: 4px;
  font-size: 12px;
  cursor: pointer;
}
.event strong {
  display: block;
  font-size: 13px;
}
.item-card {
  background: #f4f4f4;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 10px;
  text-align: center;
}
.item-card img {
  width: 50px;
  height: 50px;
  object-fit: contain;
}
.calendar-grid {
  display: grid;
  grid-template-columns: 100px repeat(5, 1fr);
  gap: 4px;
  margin-top: 20px;
}
.time-slot {
  background: #f0f0f0;
  font-size: 12px;
  padding: 4px;
  border-right: 1px solid #ccc;
  text-align: right;
  padding-right: 8px;
}
}
</style>