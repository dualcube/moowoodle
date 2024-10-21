import { useState, useEffect, useRef } from "react";
import { sendApiResponse, getApiLink } from "../../../../../services/apiService";
import './ConnectButton.scss';
import { Link } from "react-router-dom";

const ConnectButton = (props) => {
    const { __ } = wp.i18n;
    const { apiLink, tasks } = props;
    const connectTaskStarted = useRef(false);
    const additionalData = useRef({});
    const taskNumber = useRef(0);
    const [loading, setLoading] = useState(false);
    const [taskSequence, setTaskSequence] = useState([]);
    const [testStatus, setTestStatus] = useState('');

    // Sleep for a given time.
    const sleep = (time) => {
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                resolve();
            }, time)
        });
    }

    // Sequence task
    // const tasks = [
    //     {
    //         'action': 'get_site_info',
    //         'message': __('Connecting to Moodle', 'moowoodle'),
    //     },
    //     {
    //         'action': 'get_course',
    //         'message': __('Courses Fetch', 'moowoodle'),
    //         'cache': 'course_id',
    //     },
    //     {
    //         'action': 'get_catagory',
    //         'message': __('Catagory Fetch', 'moowoodle'),
    //     },
    //     {
    //         'action': 'create_user',
    //         'message': __('User Creation', 'moowoodle'),
    //     },
    //     {
    //         'action': 'get_user',
    //         'message': __('User Fetch', 'moowoodle'),
    //         'cache': 'user_id',
    //     },
    //     {
    //         'action': 'update_user',
    //         'message': __('User Update', 'moowoodle'),
    //     },
    //     {
    //         'action': 'enroll_user',
    //         'message': __('User Enroll', 'moowoodle'),
    //     },
    //     {
    //         'action': 'unenroll_user',
    //         'message': __('User Unenroll', 'moowoodle'),
    //     },
    //     {
    //         'action': 'delete_user',
    //         'message': __('User Remove', 'moowoodle'),
    //     }
    // ];

    const startConnectionTask = async () => {
        // Connection task is already running
        if (connectTaskStarted.current) {
            return;
        }

        connectTaskStarted.current = true;
        setLoading(true);

        setTaskSequence([]);

        await doSequencialTask();

        connectTaskStarted.current = false;
        setLoading(false);
    }

    const doSequencialTask = async () => {
        // There is no task to display
        if (taskNumber.current >= tasks.length) {
            setTestStatus('Test Successful');
            return;
        }

        const currentTask = tasks[taskNumber.current];

        // Set the task sequence to current task.
        setTaskSequence((taskes) => {
            return [
                ...taskes,
                {
                    name: currentTask.action,
                    message: currentTask.message,
                    status: 'running',
                }
            ];
        });

        await sleep(2500);

        const response = await sendApiResponse(
            getApiLink(apiLink),
            {
                action: currentTask.action,
                ...additionalData.current,
            },
        );
        
        // Evelute task status
        let taskStatus = 'success';

        // Collect course id
        if (currentTask.cache === 'course_id') {
            const validCourse = response?.courses?.[1];

            if (!validCourse) {
                taskStatus = 'failed';
            } else {
                additionalData.current['course_id'] = validCourse.id;
            }
        }
        // Collect user id
        else if (currentTask.cache === 'user_id') {
            const validUser = response?.data?.users?.[0];

            if (!validUser) {
                taskStatus = 'failed';
            } else {
                additionalData.current['user_id'] = validUser.id;
            }
        }
        // Check where it is a success of failure
        else if (!response.success) {
            taskStatus = 'failed';
        }

        // Update task status
        setTaskSequence((tasks) => {
            const updatedTask = [...tasks];
            updatedTask[updatedTask.length - 1]['status'] = taskStatus;
            return updatedTask;
        });

        // If task status is not success exist from task sequence
        if (taskStatus === 'failed') {
            setTestStatus('Failed');
            return;
        }

        taskNumber.current++;

        // Call next task recursively
        await doSequencialTask();
    }

    return (
        <div className="connection-test-wrapper">
            <div className="section-connection-test">
                <button
                    // className="disable"
                    onClick={(e) => {
                        e.preventDefault();
                        startConnectionTask();
                    }}
                >
                    {__('Start test', 'moowoodle')}
                </button>
                {
                    loading &&
                    <div class="loader">
                        <div class="three-body__dot"></div>
                        <div class="three-body__dot"></div>
                        <div class="three-body__dot"></div>
                    </div>
                }
            </div>
            <div className="fetch-details-wrapper">
                {taskSequence.map((task) => {
                    return (
                        <div className={`${task.status} details-status-row`}>{task.message} {task.status !== "running" && <i className={`admin-font ${task.status === "failed" ? "font-cross" : "font-icon-yes"}`}></i>}</div>
                    );
                })}
                {/* {
                testStatus &&
                <div className={`fetch-display-output ${testStatus == 'Failed' ? 'failed': 'success' }`}> {testStatus} {testStatus == 'Failed' ? <i className="admin-font font-cross"></i> : <i className="admin-font font-icon-yes"></i> }</div>
            } */}
            </div>
            {
                testStatus &&
                <div className={`fetch-display-output ${testStatus === 'Failed' ? 'failed' : 'success'}`}>
                    {testStatus === 'Failed'
                        ? (
                            <p>
                                Test connection failed. Check further details in <Link className="errorlog-link" to={'?page=moowoodle#&tab=settings&sub-tab=log'}>error log</Link>.
                            </p>
                        )
                        : 'Test connection successful'}
                </div>
            }
        </div>
    );
}

export default ConnectButton;