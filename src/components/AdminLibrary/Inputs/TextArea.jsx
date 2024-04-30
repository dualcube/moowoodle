const TextArea = (props) => {
    return (
        <>
            <div className={props.wrapperClass}>
                <textarea
                    className=  {props.inputClass}
                    id=         {props.id}
                    key=        {props.key}
                    name=       {props.name}
                    value=      {props.value}
                    maxLength=  {props.maxLength}
                    placeholder={props.placeholder}
                    rows=       {props.rowNumber || "4"}
                    cols=       {props.colNumber || "50"}
                    onChange=   {(e) => { props.onChange?.(e) }}
                    onClick=    {(e) => { props.onClick?.(e) }}
                    onMouseOver={(e) => { props.onMouseOver?.(e) }}
                    onMouseOut= {(e) => { props.onMouseOut?.(e) }}
                    onFocus=    {(e) => { props.onFocus?.(e) }}
                />
                 {
                    props.proSetting && <span className="admin-pro-tag">pro</span>
                }
                {
                    props.description &&
                    <p className={props.descClass} dangerouslySetInnerHTML={{__html: props.description}}>
                        
                    </p>
                }
            </div>
        </>
    );
}

export default TextArea;