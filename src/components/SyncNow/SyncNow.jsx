const SyncNow = ( props ) => {
    
    const handleUserSync = (event) => {
        
    }

    const handleCourseSync = (event) => {

    }

    return (
        <>
            <div>
                <button
                    onClick={handleUserSync}
                >
                    All User
                </button>
                <div className="">

                </div>
            </div>
            <div>
                <button
                    onClick={handleCourseSync}
                >
                    All Course
                </button>
                <div className="">

                </div>
            </div>
        </>
    )
}

export default SyncNow;