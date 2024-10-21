import React, { useState, useEffect, useRef } from 'react';
import './MergeComponent.scss';
import Select from 'react-select';

const MergeComponent = (props) => {
    const { wrapperClass, descClass, description, onChange, value, proSetting } = props;
    const firstRender = useRef(true);
    const [data, setData] = useState({
        'wholesale_discount_type' : value.wholesale_discount_type || '',
        'wholesale_amount' : value.wholesale_amount || '',
        'minimum_quantity' : value.minimum_quantity ||''
    });

    const handleOnChange = (key, value) => {
        setData((previousData) => {
            return{...previousData, [key] : value }
        })
    }

    useEffect(() => {
        if (firstRender.current) {
            firstRender.current = false;
            return; // Prevent the initial call
        }
        onChange(data)
      }, [data]);

    return (
        <>
            <main className={wrapperClass}>
                <section className='select-input-section merge-components'>
                    <select id="wholesale_discount_type" value={data.wholesale_discount_type} onChange={(e) => handleOnChange('wholesale_discount_type', e.target.value)}>
                        <option value="">Select</option>
                        <option value="fixed_amount"> Fixed Amount</option>
                        <option value="percentage_amount"> Percentage Amount</option>
                    </select>

                    <input type="number" id="wholesale_amount" placeholder='Discount value' min="1" value={data.wholesale_amount} onChange={(e) => handleOnChange('wholesale_amount', e.target.value)}/>
                    
                    <input type="number" id="minimum_quantity" min="1" placeholder='Minimum quantity' value={data.minimum_quantity} onChange={(e) => handleOnChange('minimum_quantity', e.target.value)}/>
                </section>
                {
                    description &&
                    <p className={descClass} dangerouslySetInnerHTML={{ __html: description }} >
                    </p>
                }
                { proSetting && <span className="admin-pro-tag">pro</span> }
            </main>
        </>
    )
}

export default MergeComponent