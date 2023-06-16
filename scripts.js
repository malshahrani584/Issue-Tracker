document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("export-button").addEventListener("click", function () {
        exportTableToExcel("issue-table", "Issue_Tracker");
    });

    document.getElementById("add-issue-form").addEventListener("submit", function (event) {
        event.preventDefault();
        addIssue();
    });

    const updateButtons = document.querySelectorAll(".update-button");

    updateButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const row = this.closest("tr");
            const issueId = row.getAttribute("data-issue-id");
            const editableCells = row.querySelectorAll(".editable-cell");

            if (this.textContent === "Update") {
                editableCells.forEach((cell) => {
                    cell.setAttribute("contenteditable", "true");
                    cell.classList.add("editing");
                });
                this.textContent = "Save";
            } else {
                editableCells.forEach((cell) => {
                    cell.removeAttribute("contenteditable");
                    cell.classList.remove("editing");
                });

                const updatedData = {
                    id: issueId,
                    subject: row.querySelector('[data-field="subject"]').textContent.trim(),
                    description: row.querySelector('[data-field="description"]').textContent.trim(),
                    tickets_number: row.querySelector('[data-field="tickets_number"]').textContent.trim(),
                    pm_number: row.querySelector('[data-field="pm_number"]').textContent.trim(),
                    email_subject: row.querySelector('[data-field="email_subject"]').textContent.trim(),
                    status: row.querySelector('[data-field="status"]').textContent.trim(),
                };

                fetch('update_issue.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(updatedData)
                })
                .then(response => {
                    if (!response) {
                        throw new Error('No response from the server.');
                    }

                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error('Network response was not ok.');
                    }
                })
                .then(data => {
                    if (data.success) {
                        console.log(data.message);

                        const cells = row.querySelectorAll('.editable-cell');
                        cells[0].textContent = updatedData.subject;
                        cells[1].textContent = updatedData.description;
                        cells[2].textContent = updatedData.tickets_number;
                        cells[3].textContent = updatedData.pm_number;
                        cells[4].textContent = updatedData.email_subject;
                        cells[5].textContent = updatedData.status;
                    } else {
                        console.error(data.message);
                    }
                })
                .catch(error => {
                    console.error('There has been a problem with your fetch operation:', error);
                });

                this.textContent = "Update";
            }
        });
    });

    // Add an event listener for the "Add a new issue" button
    document.getElementById("show-add-issue-form-button").addEventListener("click", function () {
        const form = document.getElementById("add-issue-form");
        if (form.style.display === "none") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    });
});

async function addIssue() {
    const formData = new FormData(document.getElementById("add-issue-form"));

    try {
        const response = await fetch("add_issue.php", {
            method: "POST",
            body: formData
        });

        if (!response) {
            throw new Error("No response from the server.");
        }

        if (response.ok) {
            const issue = await response.json();
            const newRow = document.createElement("tr");
            newRow.setAttribute("data-issue-id", issue.id);
            newRow.innerHTML = `
                <td>${issue.id}</td>
                <td>${issue.service_name}</td>
                <td class="editable-cell">${issue.subject}</td>
                <td class="editable-cell">${issue.description}</td>
                <td class="editable-cell">${issue.tickets_number}</td>
                <td class="editable-cell">${issue.pm_number}</td>
                <td class="editable-cell">${issue.email_subject}</td>
                <td>${issue.create_date}</td>
                <td>${issue.username}</td>
                <td class="editable-cell">${issue.status}</td>
                <td><button class="update-button">Update</button></td>
            `;
            document.querySelector("#issue-table tbody").appendChild(newRow);

            document.getElementById("add-issue-form").reset();
        } else {
            console.error("Failed to add issue.");
        }
    } catch (error) {
        console.error("Error in addIssue():", error);
    }
}

function exportTableToExcel(tableID, filename) {
    let downloadLink;
    let dataType = "application/vnd.ms-excel";
    let tableSelect = document.getElementById(tableID);
    let tableHTML = tableSelect.outerHTML.replace(/ /g, "%20");

    filename = filename ? filename + ".xls" : "export.xls";

    downloadLink = document.createElement("a");

    document.body.appendChild(downloadLink);

    if (navigator.msSaveOrOpenBlob) {
        let blob = new Blob(["\ufeff", tableHTML], {
            type: dataType,
        });
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        downloadLink.href = "data:" + dataType + ", " + tableHTML;

        downloadLink.download = filename;

        downloadLink.click();
    }
}