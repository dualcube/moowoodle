import { Calendar } from "react-multi-date-picker";

const CalendarInput = (props) => {
    return (
        <>
            <div className={props.wrapperClass}>
                <Calendar
                    className=  {props.inputClass}
                    format=     {props.format || "D/M/YYYY"}
                    multiple=   {props.multiple && true}
                    value=      {props.value || ''}
                    onChange=   {(e) => { props.onChange?.(e) }}
                />
            </div>
        </>
    );
}

export default CalendarInput;