const BlockText = (props) => {
    return (
        <>
            <div className={props.wrapperClass}>
                <p className={props.blockTextClass} dangerouslySetInnerHTML={{ __html: props.value }}>
                </p>
            </div>
        </>
    );
}

export default BlockText;