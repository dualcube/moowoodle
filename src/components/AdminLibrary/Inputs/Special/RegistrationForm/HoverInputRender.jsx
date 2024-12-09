import { useState, useEffect } from 'react';

const HoverInputRender = ({
    label,
    placeholder,
    onLabelChange,
    renderStaticContent,
    renderEditableContent,
}) => {
    const [showTextBox, setShowTextBox] = useState(false);
    const [isClicked, setIsClicked] = useState(false);
    let hoverTimeout = null;

    useEffect(() => {
        const closePopup = (event) => {
            if (event.target.closest('.meta-setting-modal, .react-draggable')) {
                return;
            }
            setIsClicked(false);
            setShowTextBox(false);
        };
        document.body.addEventListener("click", closePopup);
        return () => {
            document.body.removeEventListener("click", closePopup);
        };
    }, []);

    const handleMouseEnter = () => {
        hoverTimeout = setTimeout(() => setShowTextBox(true), 300);
    };

    const handleMouseLeave = () => {
        clearTimeout(hoverTimeout); // Clear the timeout if mouse leaves early
        if (!isClicked) setShowTextBox(false);
    };

    return (
        <>
            {!showTextBox && (
                <div
                    onMouseEnter={handleMouseEnter}
                    onMouseLeave={handleMouseLeave}
                    style={{ cursor: 'pointer' }}
                >
                    {renderStaticContent({ label, placeholder })}
                </div>
            )}
            {showTextBox && (
                <div
                    className="main-input-wrapper"
                    onClick={() => setIsClicked(true)}
                    onMouseLeave={handleMouseLeave}
                >
                    {renderEditableContent({ label, onLabelChange, placeholder })}
                </div>
            )}
        </>
    );
};

export default HoverInputRender;
