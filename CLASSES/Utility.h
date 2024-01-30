#ifndef UTILITY_H
#define UTILITY_H

#include <string>
#include <vector>
#include <thread>
#include <stdexcept>
// Function declarations
void computeJacobianPortion(const std::vector<float>& output, 
                            const std::vector<float>& dvalues,
                            std::vector<std::vector<float>>& jacobian,
                            int startRow, 
                            int endRow);
std::string readJsonFromFile(const std::string& filePath);
std::vector<std::vector<float>> extractMatrixData(const std::string& jsonData, const char* key);
std::vector<float> extractVectorData(const std::string& jsonData, const char* key);
// std::vector<std::vector<float>> jacobianMatrix1D(const std::vector<float>& output, const std::vector<float>& dvalues);
// std::vector<std::vector<float>> jacobianMatrix2D(const std::vector<std::vector<float>>& output, const std::vector<std::vector<float>>& dvalues);

template<typename T>
bool saveToJSON(const std::string& filename, const std::vector<std::vector<T>>& data);

#endif // UTILITY_H