// -------------------------
// Sidebar Toggle
// -------------------------
function toggleMenu() {
    document.getElementById('sidebar').classList.toggle('active');
}

// -------------------------
// Notification System
// -------------------------
function showNotification(message, link = "#") {
    const container = document.getElementById('notificationContainer');
    const notification = document.createElement('div');
    notification.classList.add('notification');
    notification.innerHTML = message;
    notification.onclick = () => { window.location.href = link; };
    container.appendChild(notification);

    // Neon fade-in effect
    notification.style.opacity = 0;
    notification.style.transform = "translateY(-10px)";
    setTimeout(() => {
        notification.style.transition = "all 0.5s ease";
        notification.style.opacity = 1;
        notification.style.transform = "translateY(0)";
    }, 10);

    setTimeout(() => { container.removeChild(notification); }, 5000);
}



// -------------------------
// Animated Number Counter
// -------------------------
function animateValue(id, start, end, duration) {
    let obj = document.getElementById(id);
    if(!obj) return;
    let range = end - start;
    let stepTime = Math.abs(Math.floor(duration / (range || 1)));
    let current = start;
    let increment = end > start ? 1 : -1;

    let timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        if (id === "earnings" || id === "totalSales") {
            obj.innerText = `$${current.toLocaleString()}`;
        } else {
            obj.innerText = current.toLocaleString();
        }
    }, stepTime);
}

// -------------------------
// Initial Dashboard Animation
// -------------------------
// animateValue("dailyViews", 0, 1042, 1500);
// animateValue("sales", 0, 80, 1500);
// animateValue("comments", 0, 208, 1500);
// animateValue("earnings", 0, 6042, 1500);
// animateValue("totalSales", 0, 12500, 1500);

// -------------------------
// Live Activity Feed
// -------------------------
const activities = [
    "New customer signed up",
    "Order #1024 has been paid",
    "Comment added to Sales Dashboard",
    "New message received",
    "Server load increased",
    "Product stock running low",
    "Sales target achieved"
];

function addActivity(activityText) {
    if (!activityText) return; // Only add if you pass a real event

    const feed = document.getElementById("activityList");
    const li = document.createElement("li");
    li.innerText = activityText;

    // Neon fade-in animation
    li.style.opacity = 0;
    li.style.transform = "translateY(-10px)";
    feed.prepend(li);
    setTimeout(() => {
        li.style.transition = "all 0.5s ease";
        li.style.opacity = 1;
        li.style.transform = "translateY(0)";
    }, 10);

    if(feed.children.length > 10) feed.removeChild(feed.lastChild);
}

// Example usage: call this whenever something happens
function onNewCustomer(name) {
    addActivity(`New customer "${name}" added`);
}

function onNewOrder(orderId) {
    addActivity(`Order #${orderId} has been placed`);
}

function onDailyVisitAdded(count) {
    addActivity(`Added ${count} daily visits`);
}

function onFollowupCreated(customerName) {
    addActivity(`New follow-up created for ${customerName}`);
}

// -------------------------
// Charts - Sales and Customer Growth
// -------------------------
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
        datasets: [{
            label: 'Sales',
            data: [120,150,180,200,170,220,250],
            backgroundColor: 'rgba(0,255,255,0.2)',
            borderColor: 'rgba(0,255,255,1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
        }]
    },
    options: { responsive:true, animation:{duration:1000}, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true } } }
});

const customerCtx = document.getElementById('customerChart').getContext('2d');
const customerChart = new Chart(customerCtx, {
    type: 'bar',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul'],
        datasets: [{
            label: 'Customers',
            data: [50,70,150,120,200,180,220],
            backgroundColor: 'rgba(0,255,255,0.5)',
            borderColor: 'rgba(0,255,255,1)',
            borderWidth: 1
        }]
    },
    options: { responsive:true, animation:{duration:1000}, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true } } }
});

// -------------------------
// Refresh Dashboard Function
// -------------------------
function refreshDashboardData() {
//     // animateValue("dailyViews", parseInt(document.getElementById("dailyViews").innerText.replace(/,/g, "")), Math.floor(Math.random() * 5000 + 500), 1000);
//     // animateValue("sales", parseInt(document.getElementById("sales").innerText), Math.floor(Math.random() * 200 + 50), 1000);
//     animateValue("comments", parseInt(document.getElementById("comments").innerText), Math.floor(Math.random() * 300 + 50), 1000);
//     // animateValue("earnings", parseInt(document.getElementById("earnings").innerText.replace("$", "")), Math.floor(Math.random() * 10000 + 500), 1000);

    // Update charts
    salesChart.data.datasets[0].data = salesChart.data.datasets[0].data.map(() => Math.floor(Math.random() * 300 + 100));
    salesChart.update();

    customerChart.data.datasets[0].data = customerChart.data.datasets[0].data.map(() => Math.floor(Math.random() * 250 + 50));
    customerChart.update();
}

// -------------------------
// Status Update Function
// -------------------------
function updateStatus(id, newStatus) {
    console.log(`Status of ${id} changed to ${newStatus}`);
    
    // Update dashboard data and activity feed
    refreshDashboardData();
    addActivity(`Status of ${id} updated to ${newStatus}`);
    showNotification(`Status of ${id} updated`, "#dashboard");
}

// -------------------------
// Add Event Listeners to Status Buttons
// -------------------------
// Example: all buttons with class "status-btn" should have data-id and data-status
document.querySelectorAll(".status-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        const id = btn.getAttribute("data-id");
        const status = btn.getAttribute("data-status");
        updateStatus(id, status);
    });
});

async function updateTotalSales() {
  try {
    const response = await fetch('AddValue.php');
    const data = await response.json();

    if (data && data.totalSales !== undefined) {
      document.getElementById('sales').innerText =
        "₹" + Number(data.totalSales).toLocaleString(undefined, { minimumFractionDigits: 2 });
    } else {
      console.error("Invalid response:", data);
    }
  } catch (error) {
    console.error("Error fetching total sales:", error);
  }
}

// Call once on load
updateTotalSales();

// Optional: Auto-refresh every 30 seconds
setInterval(updateTotalSales, 30000);


 async function loadRecentOrders() {
    try {
      const response = await fetch('recentOrder.php');
      const data = await response.json();

      const table = document.getElementById('recentOrdersTable');
      table.innerHTML = ''; // clear old rows

      if (data.length === 0) {
        table.innerHTML = '<tr><td colspan="4" style="text-align:center;">No recent orders found.</td></tr>';
        return;
      }

      data.forEach(order => {
        const row = `
          <tr>
            <td>${order.Product_Name}</td>
            <td>₹${Number(order.Total_Value).toLocaleString()}</td>
            <td>${order.Quantity}</td>
            <td>₹${Number(order.GST).toLocaleString()}</td>
          </tr>`;
        table.insertAdjacentHTML('beforeend', row);
      });
    } catch (error) {
      console.error('Error loading recent orders:', error);
    }
  }

  // Load on page load
  loadRecentOrders();


async function updateCustomerCount() {
  try {
    const response = await fetch('custCount.php');
    const data = await response.json();

    if (data && data.totalCustomers !== undefined) {
      document.getElementById('dailyViews').innerText =
        Number(data.totalCustomers).toLocaleString();
    } else {
      console.error("Invalid response:", data);
    }
  } catch (error) {
    console.error("Error fetching customer count:", error);
  }
}

// Load once on page load
updateCustomerCount();

// Optional: Auto-refresh every 30 seconds
setInterval(updateCustomerCount, 30000);


async function updateDailyVisits() {
    const target = document.getElementById('dailyVisits');
    if (!target) return; // stop if element not found

    try {
        const response = await fetch('visit_count.php');
        const data = await response.json();

        if (data && typeof data.totalVisits !== "undefined") {
            target.innerText = Number(data.totalVisits).toLocaleString();
        } else {
            target.innerText = "0";
        }
    } catch (error) {
        console.error("Error fetching daily visits:", error);
        target.innerText = "0";
    }
}

document.addEventListener("DOMContentLoaded", () => {
    updateDailyVisits();
    setInterval(updateDailyVisits, 30000);
});


async function updateFollowupsCard() {
    try {
        const response = await fetch('followups_count.php');
        const data = await response.json();
        const target = document.getElementById('comments');
        
        if (data.success) {
            target.innerText = data.totalFollowups;
        } else {
            target.innerText = "0";
            console.error("Error:", data.message);
        }
    } catch (err) {
        console.error("Fetch error:", err);
        document.getElementById('comments').innerText = "0";
    }
}

// Run after page load
document.addEventListener('DOMContentLoaded', updateFollowupsCard);

document.addEventListener("DOMContentLoaded", () => {
  const themeSelect = document.getElementById("themeSelect");
  const body = document.body;
  const profileInput = document.getElementById("profileInput");
  const profilePreview = document.getElementById("profilePreview");

  // Default theme set to neon
  if (!body.classList.contains("light") && !body.classList.contains("dark") && !body.classList.contains("neon")) {
    body.classList.add("neon");
  }

  // Live theme change
  themeSelect.addEventListener("change", () => {
    body.className = themeSelect.value;
  });

  // Live profile picture preview
    if (input && preview) {
        input.addEventListener("change", (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => { preview.src = ev.target.result; };
                reader.readAsDataURL(file);
            }
        });
    }
});
