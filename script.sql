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
	notes TEXT, 
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
    CONSTRAINT fk_academy_owner FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
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
    CONSTRAINT fk_student_user_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_student_user_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT unique_student_user UNIQUE (user_id, student_id)
);

CREATE TABLE academy_students (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    academy_id UUID NOT NULL,
    student_id UUID NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    CONSTRAINT fk_academy_students_academy FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE,
    CONSTRAINT fk_academy_students_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT unique_academy_student UNIQUE (academy_id, student_id)
);

CREATE TABLE belt_ranks (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    academy_id UUID NOT NULL,
    description VARCHAR(50) NOT NULL,
    level INT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    CONSTRAINT fk_belt_academy FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE
);


CREATE TABLE student_belt_ranks (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    student_id UUID NOT NULL,
    academy_id UUID NOT NULL,
    belt_rank_id UUID NOT NULL,
    awarded_at DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT NOW(),
    CONSTRAINT fk_sbr_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_sbr_academy FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE,
    CONSTRAINT fk_sbr_belt FOREIGN KEY (belt_rank_id) REFERENCES belt_ranks(id) ON DELETE CASCADE
);

CREATE TABLE wall_posts (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    academy_id UUID NOT NULL,
    user_id UUID NOT NULL, 
    title VARCHAR(150),
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    is_active BOOLEAN DEFAULT TRUE,
    CONSTRAINT fk_wall_post_academy FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE,
    CONSTRAINT fk_wall_post_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Modalidades (ex: Jiu-Jitsu, Muay Thai, Funcional...)
CREATE TABLE class_modalities (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    academy_id UUID NOT NULL,
    name VARCHAR(80) NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    is_active BOOLEAN DEFAULT TRUE,
    CONSTRAINT fk_modality_academy FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE
);

-- Grade fixa semanal (ex: toda segunda e quarta às 19h)
CREATE TABLE class_schedules (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    academy_id UUID NOT NULL,
    modality_id UUID,
    weekday SMALLINT NOT NULL CHECK (weekday BETWEEN 0 AND 6), -- 0=Dom, 1=Seg... 6=Sab
    start_time TIME NOT NULL,
    end_time TIME,
    notes TEXT,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    is_active BOOLEAN DEFAULT TRUE,
    CONSTRAINT fk_schedule_academy FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE,
    CONSTRAINT fk_schedule_modality FOREIGN KEY (modality_id) REFERENCES class_modalities(id) ON DELETE SET NULL
);

-- Aulas realizadas (avulsas ou geradas a partir da grade)
CREATE TABLE classes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    academy_id UUID NOT NULL,
    schedule_id UUID,                 
    modality_id UUID,
    class_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    notes TEXT,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    is_active BOOLEAN DEFAULT TRUE,
    CONSTRAINT fk_class_academy FOREIGN KEY (academy_id) REFERENCES academies(id) ON DELETE CASCADE,
    CONSTRAINT fk_class_schedule FOREIGN KEY (schedule_id) REFERENCES class_schedules(id) ON DELETE SET NULL,
    CONSTRAINT fk_class_modality FOREIGN KEY (modality_id) REFERENCES class_modalities(id) ON DELETE SET NULL
);

-- Presença dos alunos por aula
CREATE TABLE class_attendances (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    class_id UUID NOT NULL,
    student_id UUID NOT NULL,
    present BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT NOW(),
    CONSTRAINT fk_attendance_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    CONSTRAINT fk_attendance_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT unique_class_student UNIQUE (class_id, student_id)
);


