import React, { useState, useEffect } from "react";
import axios from "axios";
import { getApiLink } from "../../services/apiService";
import ViewEnroll from "./ViewEnroll";
import "./MyClassroom.scss";

const MyClassroom = () => {
    const [classrooms, setClassrooms] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [selectedClassroom, setSelectedClassroom] = useState(null);
    const [editingClassroom, setEditingClassroom] = useState(null);
    const [newName, setNewName] = useState("");
    const itemsPerPage = 10;

    const fetchClassrooms = async () => {
        try {
            const response = await axios.post(getApiLink("classroom"), {
                page: currentPage,
                rows: itemsPerPage,
            }, {
                headers: { "X-WP-Nonce": appLocalizer.nonce },
            });

            if (response.data.status === "success") {
                setClassrooms(response.data.groups || []);
                setTotalPages(response.data.pagination.total_pages);
            } else {
                setClassrooms([]);
            }
        } catch (error) {
            console.error("Error fetching classroom data:", error);
        }
    };

    useEffect(() => {
        fetchClassrooms();
    }, [currentPage]);

    const handlePageChange = (newPage) => {
        if (newPage >= 1 && newPage <= totalPages) {
            setCurrentPage(newPage);
        }
    };

    const handleViewEnroll = (group) => {
        setSelectedClassroom(group);
    };

    const handleBackToClassrooms = () => {
        setSelectedClassroom(null);
        fetchClassrooms();
    };

    const handleEditClick = (group) => {
        setEditingClassroom(group.group_id);
        setNewName(group.group_name);
    };

    const handleUpdateClassroom = async (group) => {
        if (!newName.trim()) return;

        try {
            const response = await axios.post(
                getApiLink("rename-classroom"),
                { id: group.group_id, new_name: newName },
                { headers: { "X-WP-Nonce": appLocalizer.nonce } }
            );

            if (response.data.status === "success") {
                setClassrooms((prevClassrooms) =>
                    prevClassrooms.map((g) =>
                        g.group_id === group.group_id ? { ...g, group_name: newName } : g
                    )
                );
                setEditingClassroom(null);
            } else {
                alert("Failed to rename classroom.");
            }
        } catch (error) {
            console.error("Error renaming classroom:", error);
        }
    };

    return (
        <div className="classroom-container">
            {selectedClassroom ? (
                <ViewEnroll 
                    classroom={selectedClassroom} 
                    onBack={handleBackToClassrooms} 
                    refreshClassrooms={fetchClassrooms} 
                />
            ) : (
                <>
                    <div className="header">
                        <h1>My Classroom</h1>
                    </div>

                    <div className="classroom-grid">
                        {classrooms.length > 0 ? (
                            classrooms.map((group) => (
                                <div key={group.group_id} className="classroom-card">
                                    <div className="classroom-title">
                                        {editingClassroom === group.group_id ? (
                                            <>
                                                <input
                                                    type="text"
                                                    value={newName}
                                                    onChange={(e) => setNewName(e.target.value)}
                                                    onKeyDown={(e) => e.key === "Enter" && handleUpdateClassroom(group)}
                                                    className="edit-input"
                                                />
                                                <button className="save-button" onClick={() => handleUpdateClassroom(group)}>Save</button>
                                            </>
                                        ) : (
                                            <>
                                                <h2>{group.group_name}</h2>
                                                <button className="edit-button" onClick={() => handleEditClick(group)}>✏️</button>
                                            </>
                                        )}
                                    </div>

                                    <ul>
                                        {group.items && group.items.length > 0 ? (
                                            group.items.map((item, index) => (
                                                <li key={index}>• {item.course_name}</li>
                                            ))
                                        ) : (
                                            <li>No courses available</li>
                                        )}
                                    </ul>

                                    <button className="view-button" onClick={() => handleViewEnroll(group)}>
                                        View
                                    </button>
                                </div>
                            ))
                        ) : (
                            <p>No classrooms found.</p>
                        )}
                    </div>

                    <div className="pagination">
                        <button onClick={() => handlePageChange(currentPage - 1)} disabled={currentPage === 1}>
                            Previous
                        </button>

                        <span className="page-info">{currentPage} / {totalPages}</span>

                        <button onClick={() => handlePageChange(currentPage + 1)} disabled={currentPage === totalPages}>
                            Next
                        </button>
                    </div>
                </>
            )}
        </div>
    );
};

export default MyClassroom;
