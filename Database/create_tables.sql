-- ================================================
-- Create Database and Table for Neuromodulation App
-- ================================================

-- Create database
CREATE DATABASE NeuromodulationDB;
GO

-- Use the database
USE NeuromodulationDB;
GO

-- Create main table
CREATE TABLE PatientForms (
    ID INT IDENTITY(1,1) PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    DOB DATE NOT NULL,
    Age INT NOT NULL,
    Q1 INT,
    Q2 INT,
    Q3 INT,
    Q4 INT,
    Q5 INT,
    Q6 INT,
    Q7 INT,
    Q8 INT,
    Q9 INT,
    Q10 INT,
    Q11 INT,
    Q12 INT,
    TotalScore INT,
    DateSubmitted DATETIME DEFAULT GETDATE()
);
GO
