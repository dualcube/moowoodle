import React, { useState } from "react";
import "./MyClassroom.scss"; // Import SCSS file

const MyClassroom = () => {
    const allGroups = [
        { id: 1, name: "Web Development", courses: ["HTML & CSS", "JavaScript"] },
        { id: 2, name: "Data Science", courses: ["Python", "Machine Learning"] },
        { id: 3, name: "Cyber Security", courses: ["Network Security", "Hacking"] },
        { id: 4, name: "Cloud Computing", courses: ["AWS Basics", "Azure"] },
        { id: 5, name: "Mobile Development", courses: ["React Native", "Flutter"] },
        { id: 6, name: "Game Development", courses: ["Unity 3D", "Unreal Engine"] },
        { id: 7, name: "AI & ML", courses: ["Neural Networks", "AI Ethics"] },
        { id: 8, name: "UI/UX Design", courses: ["Figma", "Prototyping"] },
        { id: 9, name: "Cyber Threats", courses: ["Penetration Testing", "Malware"] },
        { id: 10, name: "Embedded Systems", courses: ["Microcontrollers", "IoT"] },
        { id: 11, name: "Blockchain", courses: ["Ethereum", "Solidity"] },
        { id: 12, name: "Quantum Computing", courses: ["Quantum Algorithms", "Qubits"] }
    ];

    const itemsPerPage = 10;
    const [currentPage, setCurrentPage] = useState(1);
    const totalPages = Math.ceil(allGroups.length / itemsPerPage);

    const indexOfLastItem = currentPage * itemsPerPage;
    const indexOfFirstItem = indexOfLastItem - itemsPerPage;
    const currentGroups = allGroups.slice(indexOfFirstItem, indexOfLastItem);

    const handlePageChange = (newPage) => {
        setCurrentPage(newPage);
    };

    // Function to handle adding a new classroom
    const handleAddClassroom = () => {
        alert("Add Classroom button clicked! Implement form/modal here.");
    };

    return (
        <div className="classroom-container">
            {/* Title & Add Button */}
            <div className="header">
                <h1>My Classroom</h1>
                <button className="add-classroom-button" onClick={handleAddClassroom}>
                    + Add Classroom
                </button>
            </div>

            {/* Grid layout for square cards */}
            <div className="classroom-grid">
                {currentGroups.map((group) => (
                    <div key={group.id} className="classroom-card">
                        <h2>{group.name}</h2>
                        <ul>
                            {group.courses.map((course, index) => (
                                <li key={index}>• {course}</li>
                            ))}
                        </ul>
                        <button className="view-button">View</button>
                    </div>
                ))}
            </div>

            {/* Pagination Controls */}
            <div className="pagination">
                <button onClick={() => handlePageChange(currentPage - 1)} disabled={currentPage === 1}>
                    Previous
                </button>

                <span className="page-info">{currentPage} / {totalPages}</span>

                <button onClick={() => handlePageChange(currentPage + 1)} disabled={currentPage === totalPages}>
                    Next
                </button>
            </div>
        </div>
    );
};

export default MyClassroom;
