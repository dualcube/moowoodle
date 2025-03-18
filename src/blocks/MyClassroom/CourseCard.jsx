import React from "react";
import { __ } from "@wordpress/i18n";

const CourseCard = ({ course = {} }) => {
    const { course_name = __("Unknown Course", "moowoodle-pro"), available_quantity = 0, enrolled_students = 0 } = course;

    return (
        <div className="course-card">
            <h3>{course_name}</h3>
            <p>
                <strong>{__("Available Quantity:", "moowoodle-pro")}</strong> <span>{available_quantity}</span>
            </p>
            <p>
                <strong>{__("Enrolled Students:", "moowoodle-pro")}</strong> <span>{enrolled_students}</span>
            </p>
        </div>
    );
};

export default CourseCard;
