import React, { useState, useEffect } from "react";
import { __ } from "@wordpress/i18n";
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
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const itemsPerPage = 10;

    const fetchClassrooms = async () => {
        setLoading(true);
        setError(null);
    
        try {
            let response = await axios.get(
                getApiLink("classroom"),
                {
                    params: { page: currentPage, rows: itemsPerPage },
                    headers: { "X-WP-Nonce": appLocalizer.nonce }
                }
            );
            if (response.data.success) {
                response = response.data;
                setClassrooms(response.data.groups || []);
                setTotalPages(parseInt(response.data.total_pages) || 1);
            } else {
                setClassrooms([]);
                setTotalPages(1);
                setError(__("No classrooms found.", "moowoodle-pro"));
            }
        } catch (err) {
            console.error("Error fetching classroom data:", err);
            setError(__("Failed to load classrooms.", "moowoodle-pro"));
        }
    
        setLoading(false);
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
                getApiLink("classroom"), 
                { group_id: group.group_id, name: newName },
                { headers: { "X-WP-Nonce": appLocalizer.nonce } }
            );

            if (response.data.success) {
                setClassrooms((prevClassrooms) =>
                    prevClassrooms.map((g) =>
                        g.group_id === group.group_id ? { ...g, group_name: newName } : g
                    )
                );
                setEditingClassroom(null); // Close input field
                setNewName(""); // Clear input
            } else {
                alert(response.data.message || __("Failed to rename classroom.", "moowoodle-pro"));
            }
        } catch (error) {
            console.error("Error renaming classroom:", error);
            alert(__("An error occurred while updating the classroom.", "moowoodle-pro"));
        }
    };

    return (
        <div className="classroom-container">
            {selectedClassroom ? (
                <ViewEnroll 
                    classroom={selectedClassroom} 
                    onBack={handleBackToClassrooms} 
                />
            ) : (
                <>
                    <div className="header">
                        <h1>{__("My Classroom", "moowoodle-pro")}</h1>
                    </div>

                    {loading ? (
                        <p>{__("Loading classrooms...", "moowoodle-pro")}</p>
                    ) : error ? (
                        <p className="error-message">{error}</p>
                    ) : (
                        <>
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
                                                        <button className="save-button" onClick={() => handleUpdateClassroom(group)}>
                                                            {__("Save", "moowoodle-pro")}
                                                        </button>
                                                        <button className="cancel-button" onClick={() => setEditingClassroom(null)}>
                                                            {__("Cancel", "moowoodle-pro")}
                                                        </button>
                                                    </>
                                                ) : (
                                                    <>
                                                        <h2>{group.group_name}</h2>
                                                        <button className="edit-button" onClick={() => handleEditClick(group)}>
                                                            ✏️
                                                        </button>
                                                    </>
                                                )}
                                            </div>

                                            <ul>
                                                {group.items && group.items.length > 0 ? (
                                                    group.items.map((item, index) => (
                                                        <li key={index}>• {item.course_name}</li>
                                                    ))
                                                ) : (
                                                    <li>{__("No courses available", "moowoodle-pro")}</li>
                                                )}
                                            </ul>

                                            <button className="view-button" onClick={() => handleViewEnroll(group)}>
                                                {__("View", "moowoodle-pro")}
                                            </button>
                                        </div>
                                    ))
                                ) : (
                                    <p>{__("No classrooms found.", "moowoodle-pro")}</p>
                                )}
                            </div>

                            {/* Pagination Controls */}
                            {totalPages > 1 && (
                                <div className="pagination">
                                    <button 
                                        onClick={() => handlePageChange(currentPage - 1)} 
                                        disabled={currentPage === 1}
                                    >
                                        {__("Previous", "moowoodle-pro")}
                                    </button>

                                    <span className="page-info">
                                        {__("Page", "moowoodle-pro")} {currentPage} {__("of", "moowoodle-pro")} {totalPages}
                                    </span>

                                    <button 
                                        onClick={() => handlePageChange(currentPage + 1)} 
                                        disabled={currentPage >= totalPages}
                                    >
                                        {__("Next", "moowoodle-pro")}
                                    </button>
                                </div>
                            )}
                        </>
                    )}
                </>
            )}
        </div>
    );
};

export default MyClassroom;
