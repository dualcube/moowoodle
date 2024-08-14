const Label = (props) => {
    return (
        <>
            <div className={props.wrapperClass}>
                <label>{props.value}</label>
                <p className={props.descClass}>
                    {props.description}
                </p>
            </div>
        </>
    );
}

export default Label;