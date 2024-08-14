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
                 {
                    props.proSetting && <span className="admin-pro-tag">pro</span>
                }
            </div>
        </>
    );
}

export default CalendarInput;