import { useState } from "react";
import DatePicker from "react-multi-date-picker";

const CalendarInput = (props) => {
	let formattedDate;
	const dates = props.value.split(',');
	
	if (dates.length === 1 && !dates[0].includes(" - ")) {
		formattedDate = new Date(dates[0].trim());
	} else {
		formattedDate = dates.map((date) => {
			if (date.includes(" - ")) {
				const rangeDates = date.split(' - ');
				const startDate = new Date(rangeDates[0].trim());
				const endDate = new Date(rangeDates[1].trim());
				return [startDate, endDate];
			} else {
				return new Date(date.trim());
			}
		});
	}
	
	const [selectedDate, setSelectedDate] = useState(formattedDate || "");

	const handleDateChange = (e) => {
		setSelectedDate(e);
		props.onChange?.(e);
	};

	return (
	<div className={props.wrapperClass}>
		<DatePicker 
			className={props.inputClass}
			format={props.format || "YYYY-MM-DD"}
			multiple={props.multiple}
			range={props.range}
			value={selectedDate}
			placeholder={"YYYY-MM-DD"}
			onChange={handleDateChange}
		/>
		{props.proSetting && <span className="admin-pro-tag">pro</span>}
	</div>
	);
};

export default CalendarInput;
