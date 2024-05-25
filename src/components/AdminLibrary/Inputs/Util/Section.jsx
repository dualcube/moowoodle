const Section = (props) => {
    return (
        <>
            <div className={props.wrapperClass}>
                &nbsp;
                {props.value && (
                    <span>{props.value}</span>
                )}
            </div>
        </>
    );
}

export default Section;