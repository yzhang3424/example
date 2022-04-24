import six
import sys
sys.modules['sklearn.externals.six'] = six
import mlrose
import numpy as np
import random
import matplotlib.pyplot as plt  
import time

# Create list of coordinates
random.seed(10)
# map axis max value
weightMax = 100
valueMax = 30
# number of points to go through
numItems = 20
max_attempts = 20
max_iters = 100
repeat_times = 10

weights = random.sample(range(1,weightMax),numItems)
values = random.sample(range(1,valueMax),numItems)
# Initialize fitness function object using coords_list
fitness = mlrose.Knapsack(weights, values, max_weight_pct=0.5)

# Define optimization problem object
problem_fit = mlrose.DiscreteOpt(length=numItems, fitness_fn=fitness, maximize=True)



# Sales man problem for RHC
fitness_curve_RHC_1_mean = np.zeros((repeat_times,max_iters))

for i in range(repeat_times):
    
    best_state, best_fitness,fitness_curve_RHC_1 = mlrose.random_hill_climb(problem_fit,max_attempts=max_attempts,restarts=0,max_iters=max_iters,curve = True)
    fitness_curve_RHC_1_mean[i,0:len(fitness_curve_RHC_1)] = fitness_curve_RHC_1
    fitness_curve_RHC_1_mean[i,len(fitness_curve_RHC_1):max_iters] = fitness_curve_RHC_1[-1]


fitness_curve_RHC_1 = fitness_curve_RHC_1_mean.mean(axis=0)

plt.figure()
plt.plot(fitness_curve_RHC_1,label = "RHC")
plt.legend()
plt.xlabel('Iterations')
plt.ylabel('Fitness function')
plt.title("Knapsack problem RHC")
plt.savefig('Knapsack problem RHC.png')

# Sales man problem for SA
fitness_curve_SA_1_mean = np.zeros((repeat_times,max_iters))

for i in range(repeat_times):
    
    best_state, best_fitness,fitness_curve_SA_1 = mlrose.simulated_annealing(problem_fit,max_attempts=max_attempts,max_iters=max_iters,curve = True)
    fitness_curve_SA_1_mean[i,0:len(fitness_curve_SA_1)] = fitness_curve_SA_1
    fitness_curve_SA_1_mean[i,len(fitness_curve_SA_1):max_iters] = fitness_curve_SA_1[-1]


fitness_curve_SA_1 = fitness_curve_SA_1_mean.mean(axis=0)

plt.figure()
plt.plot(fitness_curve_SA_1,label = "RHC")
plt.legend()
plt.xlabel('Iterations')
plt.ylabel('Fitness function')
plt.title("Knapsack problem SA")
plt.savefig('Knapsack problem SA.png')


# Sales man problem for GA
# the effect of mutation probability
fitness_curve_GA_1_mean = np.zeros((repeat_times,max_iters))
fitness_curve_GA_2_mean = np.zeros((repeat_times,max_iters))
fitness_curve_GA_3_mean = np.zeros((repeat_times,max_iters))
fitness_curve_GA_4_mean = np.zeros((repeat_times,max_iters))
fitness_curve_GA_5_mean = np.zeros((repeat_times,max_iters))

for i in range(repeat_times):
    
    best_state, best_fitness,fitness_curve_GA_1 = mlrose.genetic_alg(problem_fit,mutation_prob=0.1,max_attempts=max_attempts,max_iters=max_iters,curve = True)
    fitness_curve_GA_1_mean[i,0:len(fitness_curve_GA_1)] = fitness_curve_GA_1
    fitness_curve_GA_1_mean[i,len(fitness_curve_GA_1):max_iters] = fitness_curve_GA_1[-1]
    
    best_state, best_fitness,fitness_curve_GA_2 = mlrose.genetic_alg(problem_fit,mutation_prob=0.3,max_attempts=max_attempts,max_iters=max_iters,curve = True)
    fitness_curve_GA_2_mean[i,0:len(fitness_curve_GA_2)] = fitness_curve_GA_2
    fitness_curve_GA_2_mean[i,len(fitness_curve_GA_2):max_iters] = fitness_curve_GA_2[-1]
    
    best_state, best_fitness,fitness_curve_GA_3 = mlrose.genetic_alg(problem_fit,mutation_prob=0.5,max_attempts=max_attempts,max_iters=max_iters,curve = True)
    fitness_curve_GA_3_mean[i,0:len(fitness_curve_GA_3)] = fitness_curve_GA_3
    fitness_curve_GA_3_mean[i,len(fitness_curve_GA_3):max_iters] = fitness_curve_GA_3[-1]
    
    best_state, best_fitness,fitness_curve_GA_4 = mlrose.genetic_alg(problem_fit,mutation_prob=0.7,max_attempts=max_attempts,max_iters=max_iters,curve = True)
    fitness_curve_GA_4_mean[i,0:len(fitness_curve_GA_4)] = fitness_curve_GA_4
    fitness_curve_GA_4_mean[i,len(fitness_curve_GA_4):max_iters] = fitness_curve_GA_4[-1]
    
    best_state, best_fitness,fitness_curve_GA_5 = mlrose.genetic_alg(problem_fit,mutation_prob=0.9,max_attempts=max_attempts,max_iters=max_iters,curve = True)
    fitness_curve_GA_5_mean[i,0:len(fitness_curve_GA_5)] = fitness_curve_GA_5
    fitness_curve_GA_5_mean[i,len(fitness_curve_GA_5):max_iters] = fitness_curve_GA_5[-1]

fitness_curve_GA_1 = fitness_curve_GA_1_mean.mean(axis=0)
fitness_curve_GA_2 = fitness_curve_GA_2_mean.mean(axis=0)
fitness_curve_GA_3 = fitness_curve_GA_3_mean.mean(axis=0)
fitness_curve_GA_4 = fitness_curve_GA_4_mean.mean(axis=0)
fitness_curve_GA_5 = fitness_curve_GA_5_mean.mean(axis=0)
plt.figure()
plt.plot(fitness_curve_GA_1,label = "0.1")
plt.plot(fitness_curve_GA_2,label = "0.3")
plt.plot(fitness_curve_GA_3,label ="0.5")
plt.plot(fitness_curve_GA_4,label ="0.7")
plt.plot(fitness_curve_GA_5,label ="0.9")
plt.legend()
plt.xlabel('Iterations')
plt.ylabel('Fitness function')
plt.title("Knapsack GA with different mutation probability")
plt.savefig('Knapsack GA with different mutation probability.png')



# Sales man problem for MIMIC
# the effect of proportion of samples to kkep at each iteration
fitness_curve_MIMIC_1_mean = np.zeros((repeat_times,max_iters))
fitness_curve_MIMIC_2_mean = np.zeros((repeat_times,max_iters))
fitness_curve_MIMIC_3_mean = np.zeros((repeat_times,max_iters))
fitness_curve_MIMIC_4_mean = np.zeros((repeat_times,max_iters))
fitness_curve_MIMIC_5_mean = np.zeros((repeat_times,max_iters))

for i in range(repeat_times):
    
    best_state, best_fitness,fitness_curve_MIMIC_1 = mlrose.mimic(problem_fit,keep_pct=0.1,max_attempts=max_attempts,max_iters=max_iters,curve = True)
    fitness_curve_MIMIC_1_mean[i,0:len(fitness_curve_MIMIC_1)] = fitness_curve_MIMIC_1
    fitness_curve_MIMIC_1_mean[i,len(fitness_curve_MIMIC_1):max_iters] = fitness_curve_MIMIC_1[-1]
    
    best_state, best_fitness,fitness_curve_MIMIC_2 = mlrose.mimic(problem_fit,keep_pct=0.3,max_attempts=max_attempts,max_iters=max_iters,curve = True)
    fitness_curve_MIMIC_2_mean[i,0:len(fitness_curve_MIMIC_2)] = fitness_curve_MIMIC_2
    fitness_curve_MIMIC_2_mean[i,len(fitness_curve_MIMIC_2):max_iters] = fitness_curve_MIMIC_2[-1]
    
    best_state, best_fitness,fitness_curve_MIMIC_3 = mlrose.mimic(problem_fit,keep_pct=0.5,max_attempts=max_attempts,max_iters=max_iters,curve = True)
    fitness_curve_MIMIC_3_mean[i,0:len(fitness_curve_MIMIC_3)] = fitness_curve_MIMIC_3
    fitness_curve_MIMIC_3_mean[i,len(fitness_curve_MIMIC_3):max_iters] = fitness_curve_MIMIC_3[-1]
    
    best_state, best_fitness,fitness_curve_MIMIC_4 = mlrose.mimic(problem_fit,keep_pct=0.7,max_attempts=max_attempts,max_iters=max_iters,curve = True)
    fitness_curve_MIMIC_4_mean[i,0:len(fitness_curve_MIMIC_4)] = fitness_curve_MIMIC_4
    fitness_curve_MIMIC_4_mean[i,len(fitness_curve_MIMIC_4):max_iters] = fitness_curve_MIMIC_4[-1]
    
    best_state, best_fitness,fitness_curve_MIMIC_5 = mlrose.mimic(problem_fit,keep_pct=0.9,max_attempts=max_attempts,max_iters=max_iters,curve = True)
    fitness_curve_MIMIC_5_mean[i,0:len(fitness_curve_MIMIC_5)] = fitness_curve_MIMIC_5
    fitness_curve_MIMIC_5_mean[i,len(fitness_curve_MIMIC_5):max_iters] = fitness_curve_MIMIC_5[-1]

fitness_curve_MIMIC_1 = fitness_curve_MIMIC_1_mean.mean(axis=0)
fitness_curve_MIMIC_2 = fitness_curve_MIMIC_2_mean.mean(axis=0)
fitness_curve_MIMIC_3 = fitness_curve_MIMIC_3_mean.mean(axis=0)
fitness_curve_MIMIC_4 = fitness_curve_MIMIC_4_mean.mean(axis=0)
fitness_curve_MIMIC_5 = fitness_curve_MIMIC_5_mean.mean(axis=0)
plt.figure()
plt.plot(fitness_curve_MIMIC_1,label = "0.1")
plt.plot(fitness_curve_MIMIC_2,label = "0.3")
plt.plot(fitness_curve_MIMIC_3,label ="0.5")
plt.plot(fitness_curve_MIMIC_4,label ="0.7")
plt.plot(fitness_curve_MIMIC_5,label ="0.9")
plt.legend()
plt.xlabel('Iterations')
plt.ylabel('Fitness function')
plt.title("Knapsack MIMIC with different keep proportion")
plt.savefig('Knapsack MIMIC with different keep proportion.png')


plt.figure()
plt.plot(fitness_curve_RHC_1,label = "RHC")
plt.plot(fitness_curve_SA_1,label = "SA")
plt.plot(fitness_curve_GA_1,label = "GA")
plt.plot(fitness_curve_MIMIC_1,label = "MIMIC")
plt.legend()
plt.xlabel('Iterations')
plt.ylabel('Fitness function')
plt.title("Knapsack problem with 4 algorithms")
plt.savefig('Knapsack problem with 4 algorithms.png')

weightMax = 100
valueMax = 50
# number of points to go through
numItems_list = [5,10,20,40]
max_attempts = 200
max_iters = 1000

t_RHC = np.zeros((len(numItems_list),1))
t_SA = np.zeros((len(numItems_list),1))
t_GA = np.zeros((len(numItems_list),1))
t_MIMIC = np.zeros((len(numItems_list),1))
best_fit_RHC = np.zeros((len(numItems_list),1))
best_fit_SA = np.zeros((len(numItems_list),1))
best_fit_GA = np.zeros((len(numItems_list),1))
best_fit_MIMIC = np.zeros((len(numItems_list),1))

i = 0
for numItems in numItems_list:
    

    weights = random.sample(range(1,weightMax),numItems)
    values = random.sample(range(1,valueMax),numItems)
    # Initialize fitness function object using coords_list
    fitness = mlrose.Knapsack(weights, values, max_weight_pct=0.5)
    
    # Define optimization problem object
    problem_fit = mlrose.DiscreteOpt(length=numItems, fitness_fn=fitness, maximize=True)

    
    start_time = time.time()
    best_state1, best_fitness1 = mlrose.random_hill_climb(problem_fit,max_attempts=max_attempts,max_iters=max_iters)
    t1 = time.time()-start_time
    
    start_time = time.time()
    best_state2, best_fitness2 = mlrose.simulated_annealing(problem_fit,max_attempts=max_attempts,max_iters=max_iters)
    t2 = time.time()-start_time
    
    start_time = time.time()
    best_state3, best_fitness3 = mlrose.genetic_alg(problem_fit,max_attempts=max_attempts,max_iters=max_iters)
    t3 = time.time()-start_time
    
    start_time = time.time()
    best_state4, best_fitness4 = mlrose.mimic(problem_fit,max_attempts=max_attempts,max_iters=max_iters)
    t4 = time.time()-start_time
    
    t_RHC[i] = t1 
    t_SA[i] = t2 
    t_GA[i] = t3 
    t_MIMIC[i] = t4 
    best_fit_RHC[i] = best_fitness1 
    best_fit_SA[i] = best_fitness2 
    best_fit_GA[i] = best_fitness3 
    best_fit_MIMIC[i] = best_fitness4 
    
    i += 1

plt.figure()
plt.plot(numItems_list,t_RHC,label = "RHC")
plt.plot(numItems_list,t_SA,label = "SA")
plt.plot(numItems_list,t_GA,label = "GA")
plt.plot(numItems_list,t_MIMIC,label = "MIMIC")
plt.legend()
plt.xlabel('Number of items')
plt.ylabel('Computational time (s)')
plt.title("Knapsack problem complexity vs computation time")
plt.savefig('Knapsack problem complexity vs computation time.png')

plt.figure()
plt.plot(numItems_list,best_fit_RHC,label = "RHC")
plt.plot(numItems_list,best_fit_SA,label = "SA")
plt.plot(numItems_list,best_fit_GA,label = "GA")
plt.plot(numItems_list,best_fit_MIMIC,label = "MIMIC")
plt.legend()
plt.xlabel('Number of items')
plt.ylabel('Fitness function')
plt.title("Knapsack problem complexity vs fitness")
plt.savefig('Knapsack problem complexity vs fitness.png')