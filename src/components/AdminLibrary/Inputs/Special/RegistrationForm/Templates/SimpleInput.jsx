import { useState, useEffect } from 'react'; 
const SimpleInput = (props) => {
    const { formField, onChange } = props;
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
        if (!isClicked) {
        setShowTextBox(false);
        }
    };
    
    return (
        <>
            {!showTextBox && (
                <div
                    onMouseEnter={handleMouseEnter}
                    onMouseLeave={handleMouseLeave}
                    style={{ cursor: 'pointer' }}
                >
                    <div className="edit-form-wrapper">
                        <p>{formField.label}</p>
                        <div className="settings-form-group-radio">
                            <input
                                className='input-text-section simpleInput-text-input'
                                type="text"
                                placeholder={formField.placeholder}
                            />
                        </div>
                    </div>
                </div>
            )}        
            
            {showTextBox && (
                <div
                    className='main-input-wrapper' 
                    onClick={() => setIsClicked(true)}
                    onMouseLeave={handleMouseLeave}
                >
                    {/* Render label */}
                    <input
                        className='input-label simpleInput-label'
                        type="text"
                        value={formField.label}
                        onChange={(event) => { onChange('label', event.target.value) }}
                    />                {/* Render Inputs */}
                    <input
                        className='input-text-section simpleInput-text-input'
                        type="text"
                        readOnly
                        placeholder={formField.placeholder}
                    />
                </div>
            )}
        </>
    )
}
export default SimpleInput;

