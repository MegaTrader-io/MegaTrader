import React from 'react';

const CustomControls = ({ introInstance }: { introInstance: any }) => {
    const handleNext = () => {
        introInstance.nextStep();
    };

    const handlePrev = () => {
        introInstance.previousStep();
    };

    const handleSkip = () => {
        introInstance.exit();
        console.log("El usuario saltó el tutorial.");
    };

    const handleFinish = () => {
        introInstance.exit();
        console.log("El usuario finalizó el tutorial.");
        localStorage.setItem("hasSeenIntro", "true"); // Guardar en localStorage que ya vio el tutorial
    };

    return (
        <div className="custom-intro-controls">
            <button onClick={handlePrev}>Anterior</button>
            <button onClick={handleNext}>Siguiente</button>
            <button onClick={handleSkip}>Saltar</button>
            <button onClick={handleFinish}>Finalizar</button>
        </div>
    );
};

export default CustomControls;