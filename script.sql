-- EXTENSION (PostgreSQL)
CREATE EXTENSION IF NOT EXISTS "pgcrypto";



CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(120) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    document VARCHAR(14) UNIQUE,
    document_type VARCHAR(10),
	user_type VARCHAR(20) DEFAULT 'user',
	phone_number VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE academies (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL,
    name VARCHAR(120) NOT NULL,
	phone_number VARCHAR(20), 
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    is_active BOOLEAN DEFAULT TRUE,

    CONSTRAINT fk_academy_owner
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);



CREATE TABLE students (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(120) NOT NULL,
    birth_date DATE NOT NULL,
    phone_number VARCHAR(20),
    guardian_name VARCHAR(120),
    guardian_phone VARCHAR(20),

    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    is_active BOOLEAN DEFAULT TRUE
);


CREATE TABLE student_user (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL,
    student_id UUID NOT NULL,

    created_at TIMESTAMP DEFAULT NOW(),

    CONSTRAINT fk_student_user_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    CONSTRAINT fk_student_user_student
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,

    CONSTRAINT unique_student_user UNIQUE (user_id, student_id)
);

CREATE TABLE academy_students (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    academy_id UUID NOT NULL,
    student_id UUID NOT NULL,

    created_at TIMESTAMP DEFAULT NOW(),

    CONSTRAINT fk_academy_students_academy
        FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE,

    CONSTRAINT fk_academy_students_student
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,

    CONSTRAINT unique_academy_student UNIQUE (academy_id, student_id)
);

CREATE TABLE belt_ranks (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    academy_id UUID NOT NULL,
    description VARCHAR(50) NOT NULL,
    level INT NOT NULL,

    created_at TIMESTAMP DEFAULT NOW(),

    CONSTRAINT fk_belt_academy
        FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE
);


CREATE TABLE student_belt_ranks (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    student_id UUID NOT NULL,
    academy_id UUID NOT NULL,
    belt_rank_id UUID NOT NULL,

    awarded_at DATE NOT NULL,
    notes TEXT,

    created_at TIMESTAMP DEFAULT NOW(),

    CONSTRAINT fk_sbr_student
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,

    CONSTRAINT fk_sbr_academy
        FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE,

    CONSTRAINT fk_sbr_belt
        FOREIGN KEY (belt_rank_id) REFERENCES belt_ranks(id) ON DELETE CASCADE
);

SELECT * FROM academies


