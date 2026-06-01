CREATE TABLE users (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(120) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    document VARCHAR(14) UNIQUE,
    document_type VARCHAR(10),
    user_type VARCHAR(20) DEFAULT 'user',
    phone_number VARCHAR(20) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE academies (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    user_id CHAR(36) NOT NULL,
    name VARCHAR(120) NOT NULL,
    phone_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,

    CONSTRAINT fk_academy_owner
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE students (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(120) NOT NULL,
    birth_date DATE NOT NULL,
    phone_number VARCHAR(20),
    guardian_name VARCHAR(120),
    guardian_phone VARCHAR(20),
    notes VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE student_user (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    user_id CHAR(36) NOT NULL,
    student_id CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,

    CONSTRAINT fk_student_user_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_student_user_student
        FOREIGN KEY (student_id)
        REFERENCES students(id)
        ON DELETE CASCADE,

    CONSTRAINT unique_student_user
        UNIQUE (user_id, student_id)
);

CREATE TABLE academy_students (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    academy_id CHAR(36) NOT NULL,
    student_id CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,

    CONSTRAINT fk_academy_students_academy
        FOREIGN KEY (academy_id)
        REFERENCES academies(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_academy_students_student
        FOREIGN KEY (student_id)
        REFERENCES students(id)
        ON DELETE CASCADE,

    CONSTRAINT unique_academy_student
        UNIQUE (academy_id, student_id)
);

CREATE TABLE belt_ranks (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    academy_id CHAR(36) NOT NULL,
    description VARCHAR(50) NOT NULL,
    level INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,

    CONSTRAINT fk_belt_academy
        FOREIGN KEY (academy_id)
        REFERENCES academies(id)
        ON DELETE CASCADE
);

CREATE TABLE student_belt_ranks (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    student_id CHAR(36) NOT NULL,
    academy_id CHAR(36) NOT NULL,
    belt_rank_id CHAR(36) NOT NULL,
    awarded_at DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,

    CONSTRAINT fk_sbr_student
        FOREIGN KEY (student_id)
        REFERENCES students(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_sbr_academy
        FOREIGN KEY (academy_id)
        REFERENCES academies(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_sbr_belt
        FOREIGN KEY (belt_rank_id)
        REFERENCES belt_ranks(id)
        ON DELETE CASCADE
);

CREATE TABLE wall_posts (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    academy_id CHAR(36) NOT NULL,
    user_id CHAR(36),
    title VARCHAR(150),
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,

    CONSTRAINT fk_wall_post_academy
        FOREIGN KEY (academy_id)
        REFERENCES academies(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_wall_post_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
);

CREATE TABLE class_modalities (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    academy_id CHAR(36) NOT NULL,
    name VARCHAR(80) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,

    CONSTRAINT fk_modality_academy
        FOREIGN KEY (academy_id)
        REFERENCES academies(id)
        ON DELETE CASCADE
);

CREATE TABLE class_schedules (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    academy_id CHAR(36) NOT NULL,
    modality_id CHAR(36),
    weekday SMALLINT NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,

    CONSTRAINT chk_weekday
        CHECK (weekday BETWEEN 0 AND 6),

    CONSTRAINT fk_schedule_academy
        FOREIGN KEY (academy_id)
        REFERENCES academies(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_schedule_modality
        FOREIGN KEY (modality_id)
        REFERENCES class_modalities(id)
        ON DELETE SET NULL
);

CREATE TABLE classes (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    academy_id CHAR(36) NOT NULL,
    schedule_id CHAR(36),
    modality_id CHAR(36),
    class_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,

    CONSTRAINT fk_class_academy
        FOREIGN KEY (academy_id)
        REFERENCES academies(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_class_schedule
        FOREIGN KEY (schedule_id)
        REFERENCES class_schedules(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_class_modality
        FOREIGN KEY (modality_id)
        REFERENCES class_modalities(id)
        ON DELETE SET NULL
);

CREATE TABLE class_attendances (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    class_id CHAR(36) NOT NULL,
    student_id CHAR(36) NOT NULL,
    present TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,

    CONSTRAINT fk_attendance_class
        FOREIGN KEY (class_id)
        REFERENCES classes(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_attendance_student
        FOREIGN KEY (student_id)
        REFERENCES students(id)
        ON DELETE CASCADE,

    CONSTRAINT unique_class_student
        UNIQUE (class_id, student_id)
);