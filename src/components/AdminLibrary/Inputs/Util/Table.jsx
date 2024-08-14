const Table = (props) => {
    return (
        <>
			<div className={props.wrapperClass}>
                <table className={props.tableWrapperClass}>
                    <tr className={props.trWrapperClass}>
                        {
                            props.headOptions.map((option) => {
                                return (
                                    <th className={props.thWrapperClass}>{option}</th>
                                );
                            })
                        }
                    </tr>
                    {
                        props.bodyOptions.map((options) => {
                            return (
                                <tr className={props.trWrapperClass}>
                                    {
                                        options.map((option) => {
                                            return (
                                                <td className={props.tdWrapperClass}>
                                                    <p
                                                        className={props.descClass}
                                                        dangerouslySetInnerHTML={{ __html: option }}
                                                    >
                                                    </p>
                                                </td>
                                            );
                                        })
                                    }
                                </tr>
                            );
                        })
                    }
                </table>
            </div>
        </>
    );
}

export default Table;