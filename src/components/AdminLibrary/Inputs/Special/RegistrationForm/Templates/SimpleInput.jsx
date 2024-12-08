import HoverInputRender from '../HoverInputRender';

const SimpleInput = ({ formField, onChange }) => {
    return (
        <HoverInputRender
            label={formField.label}
            placeholder={formField.placeholder}
            onLabelChange={(newLabel) => onChange('label', newLabel)}
            renderStaticContent={({ label, placeholder }) => (
                <div className="edit-form-wrapper">
                    <p>{label}</p>
                    <div className="settings-form-group-radio">
                        <input
                            className="input-text-section simpleInput-text-input"
                            type="text"
                            placeholder={placeholder}
                        />
                    </div>
                </div>
            )}
            renderEditableContent={({ label, onLabelChange, placeholder }) => (
                <>
                    <input
                        className="input-label simpleInput-label"
                        type="text"
                        value={label}
                        onChange={(event) => onLabelChange(event.target.value)}
                    />
                    <input
                        className="input-text-section simpleInput-text-input"
                        type="text"
                        readOnly
                        placeholder={placeholder}
                    />
                </>
            )}
        />
    );
};

export default SimpleInput;
