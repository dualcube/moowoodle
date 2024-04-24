import { useState, useEffect, useRef } from "react";
import { sendApiResponse, getApiLink } from "../../services/apiService";

const ConnectButton = (props) => {
    const { __ } = wp.i18n;

    const connectTaskStarted = useRef(false);
    const additionalData = useRef({});
    const taskNumber = useRef(0);
    const [taskSequence, setTaskSequence] = useState([]);

    // Sleep for a given time.
    const sleep = (time) => {
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                resolve();
            }, time)
        });
    }
    
    // Sequence task
    const tasks = [
        {
            'action': 'get_site_info',
            'message': __('Connecting to Moodle', 'moowoodle'),
        },
        {
            'action': 'get_course',
            'message': __('Courses Fetch', 'moowoodle'),
            'cache': 'course_id',
        },
        {
            'action': 'get_catagory',
            'message': __('Catagory Fetch', 'moowoodle'),
        },
        {
            'action': 'create_user',
            'message': __('User Creation', 'moowoodle'),
        },
        {
            'action': 'get_user',
            'message': __('User Fetch', 'moowoodle'),
            'cache': 'user_id',
        },
        {
            'action': 'update_user',
            'message': __('User Update', 'moowoodle'),
        },
        {
            'action': 'enroll_user',
            'message': __('User Enroll', 'moowoodle'),
        },
        {
            'action': 'unenroll_user',
            'message': __('User Unenroll', 'moowoodle'),
        },
        {
            'action': 'delete_user',
            'message': __('User Remove', 'moowoodle'),
        }
    ];
    
    const startConnectionTask = async () => {
        // Connection task is already running
        if ( connectTaskStarted.current ) {
            return;
        }
        
        connectTaskStarted.current = true;
        setTaskSequence([]);

        await doSequencialTask();

        connectTaskStarted.current = false;
    }

    const doSequencialTask = async () => {
        // There is no task to display
        if (taskNumber.current >= tasks.length) {
            return;
        }

        const currentTask = tasks[ taskNumber.current ];
        
        // Set the task sequence to current task.
        setTaskSequence((taskes) => {
            return [
                ...taskes,
                {
                    name    : currentTask.action,
                    message : currentTask.message,
                    status  : 'running',
                }
            ];
        });

        await sleep(2000);

        const response = await sendApiResponse(
            getApiLink('test-connection'),
            {
                action: currentTask.action,
                ...additionalData.current,
            }
        );

        // Evelute task status
        let taskStatus = 'success';

        // Collect course id
        if ( currentTask.cache === 'course_id' ) {
            const validCourse = response?.courses?.[1];

            if ( ! validCourse ) {
                taskStatus = 'falid';
            } else {
                additionalData.current['course_id'] = validCourse.id;
            }
        }
        // Collect user id
        else if (currentTask.cache === 'user_id') {
            const validUser = response?.data?.users?.[0];

            if ( ! validUser ) {
                taskStatus = 'falid';
            } else {
                additionalData.current['user_id'] = validUser.id;
            }
        }
        // Check where it is a success of failure
        else if ( ! response.success ) {
            taskStatus = 'faild';
        }
        
        // Update task status
        setTaskSequence((tasks) => {
            const updatedTask = [...tasks];
            updatedTask[ updatedTask.length - 1 ][ 'status' ] = taskStatus;
            return updatedTask;
        });

        // If task status is not success exist from task sequence
        if ( taskStatus === 'faild' ) {
            return;
        }

        taskNumber.current++;
        
        // Call next task recursively
        await doSequencialTask();
    }
    
    return (
        <div>
            <button
                onClick={(e) => {
                    e.preventDefault();
                    startConnectionTask();
                }}
            >
                {__('Connection test', 'moowoodle')}
            </button>
            <div>
                {taskSequence.map((task) => {
                    return (
                        <div className={task.status}>{ task.message }</div>
                    );
                })}
            </div>
        </div>
    );
}

export default ConnectButton;