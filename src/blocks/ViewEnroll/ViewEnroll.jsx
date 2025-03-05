import React, { useState, useEffect } from "react";
import axios from "axios";
import { getApiLink } from "../../services/apiService";

const ViewEnroll = ({ item, onBack }) => {
  const [enrolledUsers, setEnrolledUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState(""); // New state for success messages
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [newUser, setNewUser] = useState({ name: "", email: "" });

  useEffect(() => {
    fetchEnrollments();
  }, [item.id]);

  const fetchEnrollments = async () => {
    try {
      setLoading(true);
      const response = await axios.post(
        getApiLink("get-user-enrollments-by-group-item-id"),
        { group_item_id: item.id },
        { headers: { "X-WP-Nonce": appLocalizer.nonce } }
      );
      setEnrolledUsers(response.data.enrollments);
    } catch (error) {
      setError("Failed to load enrollments.");
    } finally {
      setLoading(false);
    }
  };

  const openModal = () => {
    setError("");
    setSuccess(""); // Clear previous messages when opening modal
    setIsModalOpen(true);
  };

  const closeModal = () => {
    setNewUser({ name: "", email: "" });
    setIsModalOpen(false);
  };

  const handleChange = (e) => {
    setNewUser({ ...newUser, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSubmitting(true);
    setError("");
    setSuccess(""); // Clear previous messages

    try {
      const response = await axios.post(
        getApiLink("enroll-user"),
        {
          group_item_id: item.id,
          name: newUser.name,
          email: newUser.email,
          course_id: item.course_id,
          order_id: item.order_id,
        },
        { headers: { "X-WP-Nonce": appLocalizer.nonce } }
      );

      if (response.data.success) {
        setSuccess(response.data.message || "User enrolled successfully.");
        await fetchEnrollments();
        setTimeout(closeModal, 2000); // Auto-close modal after 2 seconds
      } else {
        setError(response.data.message || "Enrollment failed.");
      }
    } catch (error) {
      setError("Failed to enroll user.");
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="enroll-container">
      <button onClick={onBack} className="back-btn">← Back to Groups</button>

      {loading ? <p>Loading enrollments...</p> : error ? <p className="error-message">{error}</p> : (
        <>
          <button onClick={openModal} className="add-user-btn">
            + Add User
          </button>

          <div className="table-container">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Enrollment Date</th>
                </tr>
              </thead>
              <tbody>
                {enrolledUsers.length > 0 ? (
                  enrolledUsers.map((user, index) => (
                    <tr key={index}>
                      <td>{user.name}</td>
                      <td>{user.email}</td>
                      <td>{user.date}</td>
                    </tr>
                  ))
                ) : (
                  <tr>
                    <td colSpan="3" className="no-data">No enrollment data available.</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </>
      )}

      {isModalOpen && (
        <>
          <div className="modal-overlay" onClick={closeModal}></div>
          <div className="modal-content">
            <h2>Add New User</h2>
            <form onSubmit={handleSubmit}>
              <label>Name</label>
              <input type="text" name="name" value={newUser.name} onChange={handleChange} required disabled={submitting} />

              <label>Email</label>
              <input type="email" name="email" value={newUser.email} onChange={handleChange} required disabled={submitting} />

              {/* Show success or error messages */}
              {error && <p className="error-message">{error}</p>}
              {success && <p className="success-message">{success}</p>}

              <div className="modal-actions">
                <button type="submit" className="submit-btn" disabled={submitting}>
                  {submitting ? "Adding..." : "Add"}
                </button>
                <button type="button" className="cancel-btn" onClick={closeModal} disabled={submitting}>
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </>
      )}
    </div>
  );
};

export default ViewEnroll;
