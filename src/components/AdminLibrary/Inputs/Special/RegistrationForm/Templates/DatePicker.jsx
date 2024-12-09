import HoverInputRender from '../HoverInputRender';

const Datepicker = ({ formField, onChange }) => {

    return (
        <HoverInputRender
            label={formField.label}
            placeholder="Select date"
            onLabelChange={(newLabel) => onChange('label', newLabel)}
            renderStaticContent={({ label }) => (
                <div className="edit-form-wrapper">
                    <p>{label}</p>
                    <div className="settings-form-group-radio">
                        <input type="date" readOnly />
                    </div>
                </div>
            )}
            renderEditableContent={({ label, onLabelChange, placeholder }) => (
                <>
                    {/* Editable label input */}
                    <input
                        className="input-label textArea-label"
                        type="text"
                        value={label}
                        placeholder={placeholder}
                        onChange={(event) => onLabelChange(event.target.value)}
                    />

                    <input type="date" readOnly />
                </>
            )}
        />
    );
};

export default Datepicker;
