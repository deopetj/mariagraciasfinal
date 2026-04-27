document.addEventListener("DOMContentLoaded", function () {
    async function fetchReservationStats() {
        try {
            let response = await fetch("getReservationsStats.php");
            let data = await response.json();

            // Extracting data for Daily Reservations
            const dailyLabels = Object.keys(data.dailyReservations);
            const dailyCounts = Object.values(data.dailyReservations);

            // Extracting data for Status Counts
            const statusLabels = Object.keys(data.statusCounts);
            const statusCounts = Object.values(data.statusCounts);

            // Create Daily Reservations Chart
            createPieChart("dailyChart", dailyLabels, dailyCounts, ["#ff6384", "#36a2eb", "#ffcd56"]);

            // Create Status Counts Chart
            createPieChart("statusChart", statusLabels, statusCounts, ["#ffae42", "#008000", "#ff0000","#800080"]);

        } catch (error) {
            console.error("Error fetching reservation stats:", error);
        }
    }

    function createPieChart(canvasId, labels, data, colors) {
        new Chart(document.getElementById(canvasId), {
            type: "pie",
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    fetchReservationStats(); // Call the function to load data
});