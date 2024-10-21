import React from 'react';
import './ShortCodeTable.scss';

const ShortCodeTable = (props) => {

    const { wrapperClass, descClass, description, options, optionLabel } = props;

return (
        <>
            <main className={wrapperClass}>
                <table className='shortcode-table'>
                    <thead>
                        <tr>
                            {optionLabel?.map((label, index)=>{
                                return(
                                    <th key={index}>{label}</th>
                                )
                            })}
                        </tr>
                    </thead>
                    <tbody>
                        {options.map((option, index)=>{
                            return(
                                <tr key={index}>
                                    <td><code>{option.label}</code></td>
                                    <td>{option.desc}</td>
                                </tr>
                            )
                        })}
                    </tbody>
                </table>
                {
                    description &&
                    <p className={descClass} dangerouslySetInnerHTML={{ __html: description }} >
                    </p>
                }
            </main>
        </>
    )
}

export default ShortCodeTable