import React, { useState, useEffect } from "react";
import Draggable from "react-draggable";

/**
 * Component for dropdown to select form field type.
 */
const FormFieldSelect = ({ inputTypeList, formField, onTypeChange }) => (
	<FieldWrapper label="Type">
    <select
      onChange={(event) => onTypeChange?.(event.target.value)}
      value={formField.type}
    >
      {inputTypeList.map((inputType) => (
        <option key={inputType.value} value={inputType.value}>
          {inputType.label}
        </option>
      ))}
    </select>
  </FieldWrapper>
);

/**
 * Reusable wrapper for a label and input field.
 */
const FieldWrapper = ({ label, children , className}) => (
  <article className={`modal-content-section-field ${className || ''}`}  onClick={(e) => e.stopPropagation()}>
    <p>{label}</p>
    {children}
  </article>
);

/**
 * Component for rendering input fields with labels.
 */
const InputField = ({ label, type = "text", value, onChange, className}) => (
  <FieldWrapper label={label} className={className}>
    <input
      type={type}
      value={value || ""}
      onChange={(e) => onChange(e.target.value)}
    />
  </FieldWrapper>
);

/**
 * Main settings modal component.
 */
const SettingMetaBox = (props) => {
	const { formField, inputTypeList, onChange, onTypeChange, opened } = props;
	const [hasOpened, setHasOpened] = useState(opened.click);
	// Check if the site key is valid for reCAPTCHA v3
	const isValidSiteKey = (key) => /^6[0-9A-Za-z_-]{39}$/.test(key);

	const [isSiteKeyEmpty, setIsSiteKeyEmpty] = useState(
		formField.type === 'recaptcha' && !isValidSiteKey(formField.sitekey)
	);

	useEffect(() => {
		if (formField.type === 'recaptcha') {
			onChange("disabled", isSiteKeyEmpty);
		}
	}, [isSiteKeyEmpty]); 

	useEffect(() => {
		setHasOpened(opened.click);
	}, [opened]);

	// Renders conditional fields based on `formField.type`.
	const renderConditionalFields = () => {
		const commonFields = (
		<>
			<InputField
			label="Placeholder"
			value={formField.placeholder}
			onChange={(value) => onChange("placeholder", value)}
			/>
			<InputField
			label="Character Limit"
			type="number"
			value={formField.charlimit}
			onChange={(value) => onChange("charlimit", value)}
			/>
		</>
		);

		switch (formField.type) {
		case "text":
		case "email":
		case "url":
		case "textarea":
			return (
			<>
				{commonFields}
				{formField.type === "textarea" && (
				<>
					<InputField
					label="Row"
					type="number"
					value={formField.row}
					onChange={(value) => onChange("row", value)}
					/>
					<InputField
					label="Column"
					type="number"
					value={formField.column}
					onChange={(value) => onChange("column", value)}
					/>
				</>
				)}
			</>
			);
		case "recaptcha":
			return (
			<>
				{/* <InputField
				label="API Key"
				value={formField.apikey}
				onChange={(value) => onChange("apikey", value)}
				/> */}
				<InputField
				label="Site Key"
				value={formField.sitekey}
				className={isSiteKeyEmpty ? "highlight" : ""}
				onChange={(value) => {
					onChange("sitekey", value);
					setIsSiteKeyEmpty(!isValidSiteKey(value));
				}}
				/>
				<p>
					Register your site with your Google account to obtain the{' '}
					<a href="https://www.google.com/recaptcha" target="_blank" rel="noopener noreferrer">
						reCAPTCHA script
					</a>.
				</p>

			</>
			);
		case "attachment":
			return (
			<InputField
				label="Maximum File Size"
				type="number"
				value={formField.filesize}
				onChange={(value) => onChange("filesize", value)}
			/>
			);
		default:
			return null;
		}
	};

	return (
		<div onClick={() => setHasOpened((prevState) => !prevState)}>
		<i className="admin-font adminLib-menu"></i>
		{hasOpened && (
			<Draggable>
			<section className="meta-setting-modal">
				<button
				className="meta-setting-modal-button"
				onClick={(event) => {
					event.stopPropagation();
					setHasOpened(false);
				}}
				>
				<i className="admin-font adminLib-cross"></i>
				</button>
				<main className="meta-setting-modal-content">
				<h3>Input Field Settings</h3>
				<div className="setting-modal-content-section">
					<FormFieldSelect
					inputTypeList={inputTypeList}
					formField={formField}
					onTypeChange={onTypeChange}
					/>
					<InputField
					label="Name"
					value={formField.name}
					onChange={(value) => onChange("name", value)}
					/>
					{renderConditionalFields()}
				</div>
				<div className="setting-modal-content-section">
					<FieldWrapper label="Visibility">                  
					<div className="visibility-control-container">
						<div className="tabs">
							<input
								checked={formField.type === 'recaptcha' ? !isSiteKeyEmpty : !formField.disabled}
								onChange={(e) => onChange('disabled', !e.target.checked)}
								type="radio"
								id="visible"
								name="tabs"
							/>
							<label className="tab" htmlFor="visible">Visible</label>

							<input
								checked={formField.type === 'recaptcha' ? isSiteKeyEmpty : formField.disabled}
								onChange={(e) => onChange('disabled', e.target.checked)}
								type="radio"
								id="hidden"
								name="tabs"
							/>
							<label className="tab" htmlFor="hidden">Hidden</label>

							<span className="glider" />
						</div>
					</div>
					</FieldWrapper>
					
					<FieldWrapper label="Required">
					<input
						type="checkbox"
						checked={formField.required}
						onChange={(e) => onChange("required", e.target.checked)}
					/>
					</FieldWrapper>
				</div>
				</main>
			</section>
			</Draggable>
		)}
		</div>
	);
};

export default SettingMetaBox;