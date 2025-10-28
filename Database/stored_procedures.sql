USE NeuromodulationDB;
GO

-- ================================================
-- Stored Procedures for CRUD operations
-- ================================================

-- 1️⃣ Create
CREATE OR ALTER PROCEDURE sp_AddForm
(
    @FirstName VARCHAR(50),
    @LastName VARCHAR(50),
    @DOB DATE,
    @Age INT,
    @Q1 INT, @Q2 INT, @Q3 INT, @Q4 INT, @Q5 INT, @Q6 INT, @Q7 INT, @Q8 INT, @Q9 INT, @Q10 INT, @Q11 INT, @Q12 INT,
    @TotalScore INT
)
AS
BEGIN
    INSERT INTO PatientForms (
        FirstName, LastName, DOB, Age,
        Q1, Q2, Q3, Q4, Q5, Q6, Q7, Q8, Q9, Q10, Q11, Q12,
        TotalScore
    )
    VALUES (
        @FirstName, @LastName, @DOB, @Age,
        @Q1, @Q2, @Q3, @Q4, @Q5, @Q6, @Q7, @Q8, @Q9, @Q10, @Q11, @Q12,
        @TotalScore
    );
END;
GO


-- 2️⃣ Read all
CREATE OR ALTER PROCEDURE sp_GetForms
AS
BEGIN
    SELECT *
    FROM PatientForms
    ORDER BY DateSubmitted DESC;
END;
GO


-- 3️⃣ Read single
CREATE OR ALTER PROCEDURE sp_GetFormByID
    @ID INT
AS
BEGIN
    SELECT *
    FROM PatientForms
    WHERE ID = @ID;
END;
GO


-- 4️⃣ Update
CREATE OR ALTER PROCEDURE sp_UpdateForm
(
    @ID INT,
    @FirstName VARCHAR(50),
    @LastName VARCHAR(50),
    @DOB DATE,
    @Age INT,
    @Q1 INT, @Q2 INT, @Q3 INT, @Q4 INT, @Q5 INT, @Q6 INT, @Q7 INT, @Q8 INT, @Q9 INT, @Q10 INT, @Q11 INT, @Q12 INT,
    @TotalScore INT
)
AS
BEGIN
    UPDATE PatientForms
    SET
        FirstName = @FirstName,
        LastName  = @LastName,
        DOB       = @DOB,
        Age       = @Age,
        Q1 = @Q1, Q2 = @Q2, Q3 = @Q3, Q4 = @Q4, Q5 = @Q5, Q6 = @Q6, Q7 = @Q7, Q8 = @Q8, Q9 = @Q9, Q10 = @Q10, Q11 = @Q11, Q12 = @Q12,
        TotalScore = @TotalScore
    WHERE ID = @ID;
END;
GO


-- 5️⃣ Delete
CREATE OR ALTER PROCEDURE sp_DeleteForm
    @ID INT
AS
BEGIN
    DELETE FROM PatientForms WHERE ID = @ID;
END;
GO
