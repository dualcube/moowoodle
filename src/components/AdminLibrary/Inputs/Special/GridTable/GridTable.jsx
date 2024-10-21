import React, { useState, useEffect, useRef } from 'react';
import Modal from 'react-modal';
import './GridTable.scss';

/**
 * Components render selected option shown in top section
 * @param {*} props
 */
const SelectedOptionDisplay = (props) => {
    const { selectedValues, clearSelectedValues, removeSelectedValues, setPopupOpend, popupOpend } = props;

    // Get the renderable selected value for all selected value
    const renderableSelecteValue = popupOpend ? selectedValues : selectedValues.slice(0, 1);

    return (
        <div className='selected-container'>
            <div className='selected-items-container'>
                {/* all selected values */}
                {
                    renderableSelecteValue.map((value) => (
                        <div className='selected-items'>
                            <span>{value.label}</span>
                            <div
                                className=""
                                onClick={(event) => { removeSelectedValues(value) }}
                            >
                                <i className='admin-font font-close'></i>
                            </div>
                        </div>
                    ))
                }
            </div>

            <div className='container-items-controls'>
                {/* modalOpen button */}

                {!popupOpend && selectedValues.length > 1 &&
                    <div
                        className='open-modal'
                        onClick={(event) => {
                            setPopupOpend(true);
                        }}
                    >+{Math.min(selectedValues.length - 1)}</div>
                }

                {/* selected delete button */}
                <div
                    className="clear-all-data"
                    onClick={(event) => {
                        clearSelectedValues();
                    }}
                >
                    <i className='admin-font font-close'></i>
                </div>
            </div>
        </div>
    );
}

/**
 * Components render search and display option render in button section 
 * @param {*} props 
 */
const SearchOptionDisplay = (props) => {
    const { options, filter, setFilter, insertSelectedValues, searchStarted } = props;

    const [modalOpen, setModelOpen] = useState(false);

    useEffect(() => {
        const setModalClose = () => {
            setModelOpen(false);
        }

        document.addEventListener("click", setModalClose);

        return () => {
            document.removeEventListener("click", setModalClose);
        }
    }, [])

    return (
        <>
            <div className='selected-input'>
                {/* Search section */}
                <input
                    className=''
                    placeholder='Select...'
                    value={filter}
                    onChange={(event) => {
                        setModelOpen(true);
                        setFilter(event.target.value);
                    }}
                    onClick={(e) => {
                        e.stopPropagation();
                        setModelOpen(true)
                    }}
                />

                <span>
                    <i className='admin-font font-keyboard_arrow_down'></i>
                </span>
            </div>

            {
                (modalOpen) &&
                <div className='option-container' name="" id="">
                    {/* <option value="" selected disabled>Select items</option> */}
                    {
                        !searchStarted &&
                        options.map((option) => (
                            <div
                                className='options-item'
                                value={option.value}
                                onClick={(event) => {
                                    insertSelectedValues(option);
                                    setModelOpen(false);
                                }}
                            >{option.label}</div>
                        ))
                    }
                    {
                        searchStarted &&
                        <div>Searching</div>
                    }
                </div>
            }
        </>
    );
}


const Select = (props) => {
    const { values, onChange, option, asyncGetOptions, asyncFetch = false, isMulti = true } = props;

    // State variable store all selected value by user
    const [selectedValues, setSelectedValues] = useState(values || []);

    // State variable for store option
    const [options, setOptions] = useState(option || []);

    // state variable for popup opend
    const [popupOpend, setPopupOpend] = useState(false);

    // State variable for track when search started for async sync
    const [searchStarted, setSearchStarted] = useState(false);

    // State variable for filter on options
    const [filter, setFilter] = useState('');

    // Ref variable for setting changed
    const settingChanged = useRef(false);

    // Get the options
    const getOptions = async () => {
        let allOptions = option;

        // If async fetch option is set get the option from parent component
        if (asyncFetch) {
            setSearchStarted(true);
            allOptions = await asyncGetOptions(filter);
            setSearchStarted(false);
        }

        return allOptions.filter((option) => {
            let selected = false;

            selectedValues.forEach((selectedValue) => {
                if (selectedValue.value == option.value) {
                    selected = true;
                }
            });

            return !selected;

        });
    }

    /**
     * Insert a new selected value.
     * @param {*} value 
     */
    const insertSelectedValues = (value) => {
        settingChanged.current = true;
        setSelectedValues((previousSelectedValue) => {
            return [...previousSelectedValue, value];
        })
    }

    /**
     * Remove a selected value previously selected.
     * @param {*} value 
     */
    const removeSelectedValues = (value) => {
        settingChanged.current = true;
        setSelectedValues((previousSelectedValue) => {
            return previousSelectedValue.filter((previousValue) => previousValue.value != value.value);
        })
    }

    /**
     * Clear all selected value.
     * @param {*} values 
     */
    const clearSelectedValues = () => {
        settingChanged.current = true;
        setSelectedValues([]);
    }

    /**
     * Get filtered selected value
     * @returns {array}
     */
    const getFilteredOptionValue = async () => {
        let allOptions = await getOptions();

        if (asyncFetch || !filter) {
            return allOptions;
        }

        return allOptions.filter((option) => {
            return option.value?.includes(filter)
                || option.label?.includes(filter);
        });
    }

    // Trigger onchange event when selected value changed
    useEffect(() => {
        if (settingChanged.current) {
            settingChanged.current = false;
            onChange(selectedValues);
        }
    }, [selectedValues]);

    useEffect(() => {
        getFilteredOptionValue().then((options) => {
            setOptions(options);
        });
    }, [filter, option, selectedValues]);

    Modal.setAppElement("#admin-catalog");

    const openModal = () => setPopupOpend(true);
    const closeModal = () => setPopupOpend(false);

    return (
        <>
            <main className='grid-table-main-container' id="modal-support">
                <section className='main-container'>
                    {/* Top selected option display */}
                    {
                        !popupOpend &&
                        <>
                            <SelectedOptionDisplay
                                popupOpend={popupOpend}
                                setPopupOpend={setPopupOpend}
                                selectedValues={selectedValues}
                                clearSelectedValues={clearSelectedValues}
                                removeSelectedValues={removeSelectedValues}
                            />

                            <SearchOptionDisplay
                                options={options}
                                filter={filter}
                                setFilter={setFilter}
                                insertSelectedValues={insertSelectedValues}
                                searchStarted={searchStarted}
                            />
                        </>
                    }
                    {
                        popupOpend &&
                            <Modal
                                isOpen={popupOpend}
                                onRequestClose={closeModal}
                                contentLabel="Example Modal"
                                className={'exclusion-modal'}
                            >
                                <div className='modal-close-btn' onClick={closeModal}>
                                    <i className='admin-font font-cross'></i>
                                </div>
                                <SelectedOptionDisplay
                                    popupOpend={popupOpend}
                                    setPopupOpend={setPopupOpend}
                                    selectedValues={selectedValues}
                                    clearSelectedValues={clearSelectedValues}
                                    removeSelectedValues={removeSelectedValues}
                                />
            
                                <SearchOptionDisplay
                                    options={options}
                                    filter={filter}
                                    setFilter={setFilter}
                                    insertSelectedValues={insertSelectedValues}
                                    searchStarted={searchStarted}
                                />
                            </Modal>
                    }
                </section>
            </main>
        </>
    );
}

const GridTable = (props) => {
    const { rows, columns, onChange, setting } = props;
    return (
        <>
            <table className='grid-table'>
                <thead>
                    <tr>
                        <th></th>
                        {
                            columns.map((row) => {
                                return <th>{row.label}</th>
                            })
                        }
                    </tr>
                </thead>
                <tbody>
                    {
                        rows.map((row) => {
                            // console.log(row.options)
                            return (
                                <tr>
                                    <td >{row.label}</td>
                                    {columns.map((column) => {
                                        // Find key and value for each cell.
                                        let key = column.key + "_" + row.key;
                                        let value = setting[key] || [];
                                        return (
                                            <td id='grid-table-cell' className='grid-table-cell-class' key={column.key}>
                                                {
                                                    row.options &&
                                                    <Select
                                                        values={value}
                                                        onChange={(newValue) => {
                                                            onChange(key, newValue);
                                                        }}
                                                        option={row.options}
                                                        isMulti
                                                    />
                                                }
                                                {
                                                    !row.options &&
                                                    <input
                                                        placeholder='select'
                                                        checked={setting[column.key] === row.key}
                                                        type="checkbox"
                                                        onChange={(e) => {

                                                            if (e.target.checked) {
                                                                onChange(column.key, row.key);
                                                            } else {
                                                                onChange(column.key, '');
                                                            }
                                                        }}
                                                    />
                                                }
                                            </td>
                                        )
                                    })}
                                </tr>
                            );
                        })
                    }
                </tbody>
            </table>
        </>
    )
}
export default GridTable;