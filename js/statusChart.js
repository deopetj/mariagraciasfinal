document.addEventListener("DOMContentLoaded", async function () {
    console.log("🚀 Fetching data from API...");

    let canvas = document.getElementById("statusChart");
    if (!canvas) {
        console.error("❌ Canvas with ID 'statusChart' not found.");
        return;
    }

    let ctx = canvas.getContext("2d");
    let statusChartInstance = null;

    try {
        let response = await fetch("../admin1/getReservationsStats.php");
        console.log("✅ Response received. Parsing JSON...");

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        let data = await response.json();
        console.log("📊 API Response:", data);

        const statusLabels = ["Approved", "Pending", "Rejected"];
        let statusCounts = [data.approved, data.pending];

        // Fix empty data issue
        if (statusCounts.every(count => count === 0)) {
            console.warn("⚠️ Both Approved and Pending are 0. Using default values.");
            statusCounts = [1, 1]; // Prevent empty chart
        }

        // Destroy old chart instance before creating a new one
        if (statusChartInstance) {
            statusChartInstance.destroy();
        }

        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Create the new chart
        statusChartInstance = new Chart(ctx, {
            type: "pie",
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: ["#4caf50", "#fbc02d"]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        console.log("✅ Chart updated successfully!");

    } catch (error) {
        console.error("❌ Error fetching reservation status:", error);
    }
});
