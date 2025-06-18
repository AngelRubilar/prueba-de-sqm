import React from 'react';

/**
 * Componente Skeleton para mostrar durante la carga de datos
 * Proporciona feedback visual profesional sin dependencias adicionales
 */
const SkeletonLoader = () => {
  // Estilos para la animación de shimmer
  const shimmerKeyframes = `
    @keyframes shimmer {
      0% {
        background-position: -200px 0;
      }
      100% {
        background-position: calc(200px + 100%) 0;
      }
    }
  `;

  const shimmerStyle = {
    background: 'linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%)',
    backgroundSize: '200px 100%',
    animation: 'shimmer 1.5s infinite',
  };

  // Inyectar los keyframes en el documento
  React.useEffect(() => {
    const style = document.createElement('style');
    style.textContent = shimmerKeyframes;
    document.head.appendChild(style);
    return () => document.head.removeChild(style);
  }, []);

  return (
    <div style={{
      display: 'grid',
      gridTemplateColumns: 'repeat(2, 1fr)',
      gap: 16,
      padding: 16,
    }}>
      {/* Generar 4 tarjetas skeleton */}
      {[...Array(4)].map((_, index) => (
        <div
          key={index}
          style={{
            border: '1px solid #e5e5e5',
            borderRadius: 12,
            padding: 20,
            background: '#fff',
            boxShadow: '0 2px 8px rgba(0,0,0,0.04)',
            minWidth: 600,
            minHeight: 550,
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
          }}
        >
          {/* Título skeleton */}
          <div
            style={{
              ...shimmerStyle,
              width: '60%',
              height: 24,
              borderRadius: 4,
              marginBottom: 16,
            }}
          />

          <div style={{ 
            display: 'flex', 
            flexDirection: 'row', 
            justifyContent: 'space-between', 
            width: '100%',
            gap: 16
          }}>
            {/* Lado izquierdo - Imagen y datos */}
            <div style={{ 
              width: 260, 
              display: 'flex', 
              flexDirection: 'column', 
              alignItems: 'center',
              gap: 12
            }}>
              {/* Imagen skeleton */}
              <div
                style={{
                  ...shimmerStyle,
                  width: 220,
                  height: 160,
                  borderRadius: 8,
                }}
              />
              
              {/* Datos de viento skeleton */}
              <div style={{ width: '100%', textAlign: 'center' }}>
                <div
                  style={{
                    ...shimmerStyle,
                    width: '80%',
                    height: 16,
                    borderRadius: 4,
                    margin: '0 auto 8px',
                  }}
                />
                <div
                  style={{
                    ...shimmerStyle,
                    width: '60%',
                    height: 16,
                    borderRadius: 4,
                    margin: '0 auto',
                  }}
                />
              </div>

              {/* SO2 skeleton */}
              <div
                style={{
                  ...shimmerStyle,
                  width: '70%',
                  height: 20,
                  borderRadius: 4,
                }}
              />
            </div>

            {/* Lado derecho - Gráficos */}
            <div style={{ 
              flex: 1, 
              display: 'flex', 
              flexDirection: 'column', 
              gap: 16 
            }}>
              {/* Gráfico PM10 skeleton */}
              <div>
                <div
                  style={{
                    ...shimmerStyle,
                    width: '30%',
                    height: 16,
                    borderRadius: 4,
                    marginBottom: 8,
                  }}
                />
                <div
                  style={{
                    ...shimmerStyle,
                    width: '100%',
                    height: 150,
                    borderRadius: 8,
                  }}
                />
              </div>

              {/* Gráfico pronóstico skeleton (solo para algunas estaciones) */}
              {index < 3 && (
                <div>
                  <div
                    style={{
                      ...shimmerStyle,
                      width: '40%',
                      height: 16,
                      borderRadius: 4,
                      marginBottom: 8,
                    }}
                  />
                  <div
                    style={{
                      ...shimmerStyle,
                      width: '100%',
                      height: 200,
                      borderRadius: 8,
                    }}
                  />
                </div>
              )}
            </div>
          </div>
        </div>
      ))}
    </div>
  );
};

export default SkeletonLoader;
